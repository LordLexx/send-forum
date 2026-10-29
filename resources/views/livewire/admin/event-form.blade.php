<?php

use App\Notifications\NewEvent;
use App\Notifications\NewResource;
use Illuminate\Support\Facades\Notification;
use App\Jobs\SendNewEventEmails;
use App\Models\User;
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithFileUploads;

    public $eventId = null;
    public $title = '';
    public $description = '';
    public $status = 'draft';

    public bool $is_fathers_hub = false;

    // Category fields
    public $category_select = 'Drop-in';
    public $category_custom = '';

    // Date & Time fields
    public $date = '';
    public $start_hour = '09';
    public $start_minute = '00';
    public $start_period = 'AM';
    public $end_hour = '10';
    public $end_minute = '00';
    public $end_period = 'AM';

    // Location fields
    public $locationType = 'physical';
    public $address = '';
    public $map_link = '';
    public $meeting_link = '';
    public $passcode = '';

    // Image
    public $image = null;
    public $existing_image = '';
    public $showImageUpload = false;

    // Inject the Event model directly via route model binding
    public function mount(Event $event = null): void
    {
        // Check if an existing event was passed
        if ($event && $event->exists) {
            $this->eventId = $event->id;
            $this->title = $event->title;
            $this->description = $event->description;
            $this->status = $event->status;
            $this->locationType = $event->location_type ?? 'physical';
            $this->address = $event->address;
            $this->map_link = $event->map_link;
            $this->meeting_link = $event->meeting_link;
            $this->passcode = $event->passcode;
            $this->existing_image = $event->image;
            $this->is_fathers_hub = (bool) $event->is_fathers_hub;

            // Handle Category
            $predefined = ['Drop-in', 'Workshop', 'Family Fun', 'Online Webinar'];
            if (in_array($event->category, $predefined)) {
                $this->category_select = $event->category;
            } else {
                $this->category_select = 'Other';
                $this->category_custom = $event->category;
            }

            // Parse Start Date
            if ($event->start_date) {
                $start = Carbon::parse($event->start_date);
                $this->date = $start->format('Y-m-d');
                $this->start_hour = $start->format('h');
                $this->start_minute = $start->format('i');
                $this->start_period = $start->format('A');
            }

            // Parse End Date
            if ($event->end_date) {
                $end = Carbon::parse($event->end_date);
                $this->end_hour = $end->format('h');
                $this->end_minute = $end->format('i');
                $this->end_period = $end->format('A');
            }
        }
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:draft,published,cancelled',
            'category_select' => 'required|string',
            'date' => 'required|date',
            'start_hour' => 'required',
            'start_minute' => 'required',
            'start_period' => 'required',
            'end_hour' => 'required',
            'end_minute' => 'required',
            'end_period' => 'required',
            'locationType' => 'required|in:physical,online',
            'address' => 'nullable|string|required_if:locationType,physical',
            'map_link' => 'nullable|url',
            'meeting_link' => 'nullable|url|required_if:locationType,online',
            'passcode' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'is_fathers_hub' => 'boolean',
        ];

        if ($this->category_select === 'Other') {
            $rules['category_custom'] = 'required|string|max:50';
        }

        $validated = $this->validate($rules);

        $startDateTimeString = "{$this->date} {$this->start_hour}:{$this->start_minute} {$this->start_period}";
        $endDateTimeString = "{$this->date} {$this->end_hour}:{$this->end_minute} {$this->end_period}";

        $start_date = Carbon::createFromFormat('Y-m-d h:i A', $startDateTimeString)->format('Y-m-d H:i:s');
        $end_date = Carbon::createFromFormat('Y-m-d h:i A', $endDateTimeString)->format('Y-m-d H:i:s');

        if ($end_date <= $start_date) {
            $this->addError('end_time', 'The end time must be later than the start time.');
            return;
        }

        $category = $this->category_select === 'Other' ? $validated['category_custom'] : $validated['category_select'];

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'category' => $category,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'location_type' => $validated['locationType'],
            'address' => $validated['address'],
            'map_link' => $validated['map_link'],
            'meeting_link' => $validated['meeting_link'],
            'passcode' => $validated['passcode'],
            'is_fathers_hub' => $this->is_fathers_hub, // 👈 NEW
            'user_id' => auth()->id(),
        ];

        // Handle Image Upload
        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $imagePath = $this->image->store('events', 'public');
            $data['image'] = $imagePath;
        }

        if ($this->eventId) {
            $event = Event::find($this->eventId);
            $event->update($data);
            $this->dispatch('notify', type: 'success', message: 'Event updated successfully.');
        } else {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;

            while (Event::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $data['slug'] = $slug;
            $event = Event::create($data); // <-- CHANGED: capture the created event
            $this->dispatch('notify', type: 'success', message: 'Event created successfully.');

            // Dispatch a queued job to email all approved users about the new event
            if ($validated['status'] === 'published') {
                SendNewEventEmails::dispatch($event);
            }
        }

        $this->redirect(route('admin.events'), navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.admin.event-form');
    }
};
?>

@section('page_title', $eventId ? 'Edit Event' : 'Create Event')

<div> <!-- Root element wrapper -->
    <div class="p-6 space-y-6">

        <!-- Form Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <form wire:submit="save"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6"
                    enctype="multipart/form-data">

                    <!-- Event Title Input -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Event Title <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="title" wire:model.live="title"
                            placeholder="e.g. SEND Café Monthly Drop-in"
                            class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        <p class="text-xs text-gray-400 mt-1">Keep it clear and descriptive.</p>
                    </div>

                    <!-- Category & Status Group -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category_select" class="block text-sm font-medium text-gray-700 mb-1">Category
                                <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="category_select" wire:model.live="category_select"
                                    class="w-full h-11 pl-4 pr-10 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm appearance-none">
                                    <option value="Drop-in">Drop-in</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Family Fun">Family Fun</option>
                                    <option value="Online Webinar">Online Webinar</option>
                                    <option value="Other">Other (Create Custom)</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            @if($category_select === 'Other')
                                <div class="mt-3 animate-fade-in">
                                    <label for="category_custom" class="block text-sm font-medium text-gray-700 mb-1">Custom
                                        Category Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="category_custom" wire:model.live="category_custom"
                                        placeholder="e.g. Coffee Morning"
                                        class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-orange-200 focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition outline-none text-sm">
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="status" wire:model.live="status"
                                    class="w-full h-11 pl-4 pr-10 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm appearance-none">
                                    <option value="draft">Save as Draft</option>
                                    <option value="published">Publish Immediately</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Group -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" id="date" wire:model="date"
                                class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        </div>

                        <!-- Custom Time Picker Group -->
                        <div class="grid grid-cols-2 gap-3">

                            <!-- Starts Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Starts <span
                                        class="text-red-500">*</span></label>
                                <div
                                    class="flex items-center h-11 px-2 rounded-lg bg-gray-50 border border-gray-200 focus-within:bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-200 transition">
                                    <select wire:model="start_hour"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-7 border-none focus:ring-0 focus:outline-none">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                    <span class="text-gray-400">:</span>
                                    <select wire:model="start_minute"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-9 border-none focus:ring-0 focus:outline-none">
                                        <option>00</option>
                                        <option>05</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>20</option>
                                        <option>25</option>
                                        <option>30</option>
                                        <option>35</option>
                                        <option>40</option>
                                        <option>45</option>
                                        <option>50</option>
                                        <option>55</option>
                                    </select>
                                    <select wire:model="start_period"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-10 border-none focus:ring-0 focus:outline-none">
                                        <option>AM</option>
                                        <option>PM</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Ends Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ends <span
                                        class="text-red-500">*</span></label>
                                <div
                                    class="flex items-center h-11 px-2 rounded-lg bg-gray-50 border {{ $errors->has('end_time') ? 'border-red-500 ring-2 ring-red-200 bg-red-50' : 'border-gray-200 focus-within:bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-200' }} transition">
                                    <select wire:model="end_hour"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-7 border-none focus:ring-0 focus:outline-none">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                    <span class="text-gray-400">:</span>
                                    <select wire:model="end_minute"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-9 border-none focus:ring-0 focus:outline-none">
                                        <option>00</option>
                                        <option>05</option>
                                        <option>10</option>
                                        <option>15</option>
                                        <option>20</option>
                                        <option>25</option>
                                        <option>30</option>
                                        <option>35</option>
                                        <option>40</option>
                                        <option>45</option>
                                        <option>50</option>
                                        <option>55</option>
                                    </select>
                                    <select wire:model="end_period"
                                        class="time-select appearance-none bg-transparent text-sm outline-none cursor-pointer w-10 border-none focus:ring-0 focus:outline-none">
                                        <option>AM</option>
                                        <option>PM</option>
                                    </select>
                                </div>
                                @error('end_time')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location Input (Pure Livewire Toggle) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Location <span
                                class="text-red-500">*</span></label>

                        <!-- Toggle Buttons -->
                        <div class="flex items-center space-x-2 mb-4">
                            <button type="button" wire:click="$set('locationType', 'physical')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 rounded-lg border text-sm font-medium transition {{ $locationType === 'physical' ? 'bg-orange-500 text-white border-transparent hover:bg-orange-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Physical Location
                            </button>
                            <button type="button" wire:click="$set('locationType', 'online')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 rounded-lg border text-sm font-medium transition {{ $locationType === 'online' ? 'bg-orange-500 text-white border-transparent hover:bg-orange-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Online Event
                            </button>
                        </div>

                        <!-- Physical Location Inputs -->
                        @if($locationType === 'physical')
                            <div class="space-y-4 animate-fade-in">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="address" wire:model.live="address"
                                        placeholder="e.g. The Curve, Slough Library, William St, Slough SL1 1XY"
                                        class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                </div>
                                <div>
                                    <label for="map_link" class="block text-sm font-medium text-gray-700 mb-1">Map Link
                                        <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input type="url" id="map_link" wire:model.live="map_link"
                                        placeholder="https://maps.google.com/..."
                                        class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                    <p class="text-xs text-gray-400 mt-1">A Google Maps link to help parents find the venue
                                        easily.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Online Event Input -->
                        @if($locationType === 'online')
                            <div class="space-y-4 animate-fade-in">
                                <div>
                                    <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-1">Meeting
                                        Link <span class="text-red-500">*</span></label>
                                    <input type="url" id="meeting_link" wire:model.live="meeting_link"
                                        placeholder="https://zoom.us/j/..."
                                        class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Paste the full Zoom or MS Teams link here.</p>
                                </div>
                                <div>
                                    <label for="passcode" class="block text-sm font-medium text-gray-700 mb-1">Meeting
                                        Passcode <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input type="text" id="passcode" wire:model.live="passcode" placeholder="e.g. 123456"
                                        class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Description Input -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Event Description
                            <span class="text-red-500">*</span></label>
                        <textarea id="description" wire:model.live="description" rows="5"
                            placeholder="Describe what the event is about, who should attend, and what to expect."
                            class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm resize-none"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('admin.events') }}" wire:navigate
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-orange-500 rounded-lg font-medium hover:bg-orange-600 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $eventId ? 'Update Event' : 'Save Event' }}
                        </button>
                    </div>

                </form>
            </div>

            <!-- Right Column: Sidebar Info & Image Upload -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Fathers Hub Toggle -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Fathers Hub</h3>
                    <label class="flex items-start justify-between gap-3 cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Mark as Fathers Hub item</p>
                            <p class="text-xs text-gray-500 mt-0.5">Show this event on the dedicated Fathers Hub page for dads and male carers.</p>
                        </div>
                        <button type="button" wire:click="$toggle('is_fathers_hub')"
                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $is_fathers_hub ? 'bg-emerald-500' : 'bg-gray-200' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition {{ $is_fathers_hub ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </label>
                </div>

                <!-- Image Upload -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Event Image</h3>
                    <div class="flex flex-col items-center justify-center w-full">

                        <!-- 1. If a NEW image is uploaded (Temporary Preview) -->
                        @if ($image)
                            <div class="relative w-full mb-4">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-48 object-cover rounded-xl">
                                <button wire:click="$set('image', null)"
                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- 2. If an EXISTING image is in DB AND admin hasn't clicked "Change Photo" -->
                        @elseif ($existing_image && !$showImageUpload)
                            <div class="relative w-full mb-4">
                                <img src="{{ asset('storage/' . $existing_image) }}"
                                    class="w-full h-48 object-cover rounded-xl">
                            </div>
                            <button type="button" wire:click="$set('showImageUpload', true)"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Change Photo
                            </button>

                            <!-- 3. Show the dropzone (either no image exists, or admin clicked "Change Photo") -->
                        @else
                            @if ($existing_image)
                                <!-- Show a cancel button if they clicked "Change Photo" on an existing event -->
                                <button type="button" wire:click="$set('showImageUpload', false)"
                                    class="w-full mb-3 inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition text-sm">
                                    Cancel
                                </button>
                            @endif
                            <label for="dropzone-file"
                                x-data="{ dragging: false }"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; $wire.upload('image', $event.dataTransfer.files[0])"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-200 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span>
                                        or drag and drop</p>
                                    <p class="text-xs text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                </div>
                                <input id="dropzone-file" type="file" wire:model="image" class="hidden" />
                            </label>
                        @endif

                        @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Tips & Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Event Tips</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600">Use high-quality, relevant images to attract attendees.</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600">Specify if booking is required or if they can just drop
                                in.</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600">For online events, test the meeting link beforehand.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Removes the default browser arrow and focus borders so they blend nicely */
        select.time-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: transparent;
            padding-right: 0 !important;
            border: none;
            outline: none;
            box-shadow: none !important;
        }

        /* Specifically hide the dropdown arrow in older Edge/IE */
        select.time-select::-ms-expand {
            display: none;
        }

        select.time-select:focus,
        select.time-select:focus-visible {
            outline: none;
            box-shadow: none !important;
            border: none;
        }

        /* Hide native dropdown arrow on Category & Status selects */
        select#category_select,
        select#status {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
            background-repeat: no-repeat !important;
        }

        select#category_select::-ms-expand,
        select#status::-ms-expand {
            display: none !important;
        }
    </style>
</div>
