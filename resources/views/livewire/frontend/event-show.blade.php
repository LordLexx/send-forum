<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Event;
use Carbon\Carbon;

new #[Layout('layouts.master')] class extends Component {
    public Event $event;

    // Form properties
    public $email = '';
    public $isReserved = false;

    // Inject the route model bound event
    public function mount(Event $event) {
        $this->event = $event;

        // If user is logged in, pre-fill their email and check if they already reserved
        if (auth()->check()) {
            $this->email = auth()->user()->email;

            // Check if already registered to show success state immediately
            $this->isReserved = $this->event->registrations()->where('email', $this->email)->exists();
        }
    }

    public function reserveSpot() {
        if (!auth()->check()) {
            // Validate email only for guests
            $this->validate([
                'email' => 'required|email',
            ]);
        } else {
            // Ensure logged-in user's email is used
            $this->email = auth()->user()->email;
        }

        // Save the email to the database (prevents duplicates)
        \App\Models\EventRegistration::firstOrCreate([
            'event_id' => $this->event->id,
            'email' => $this->email,
        ]);

        $this->isReserved = true;

        $this->dispatch('notify', type: 'success', message: 'You have successfully reserved your spot!');
    }
};
?>

<div>
    @php
        // Generate the share URL inside the Blade view so it has access to $event
        $eventUrl = route('events.show', $event);
    @endphp

    <!-- ===== PAGE HEADER / BREADCRUMB ===== -->
    <header class="pt-28 pb-8 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('welcome') }}#home" wire:navigate class="hover:text-primary-600 transition">Home</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('events.index') }}" wire:navigate class="hover:text-primary-600 transition">Events</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-700 font-medium truncate">{{ $event->title }}</span>
            </nav>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">

                <!-- Left Column: Event Details -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Event Image -->
                    <div class="relative rounded-2xl overflow-hidden shadow-sm h-72 md:h-96">
                        <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=1200&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white rounded-xl p-2 shadow-md text-center w-16">
                            <span class="block text-orange-600 font-bold text-sm leading-none uppercase">{{ $event->start_date->format('M') }}</span>
                            <span class="block text-gray-900 font-bold text-2xl leading-none mt-1">{{ $event->start_date->format('d') }}</span>
                        </div>
                    </div>

                    <!-- Title & Quick Info -->
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 font-medium">
                            <p class="flex items-center bg-white border border-gray-100 px-3 py-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $event->start_date->format('g:i A') }} - {{ $event->end_date ? $event->end_date->format('g:i A') : '' }}
                            </p>
                            <p class="flex items-center bg-white border border-gray-100 px-3 py-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $event->location_type === 'physical' ? $event->address : 'Online via Zoom' }}
                            </p>
                        </div>
                    </div>

                    <!-- About this Event -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>
                            About this Event
                        </h2>
                        <div class="prose max-w-none text-gray-600 space-y-4 text-sm md:text-base leading-relaxed">
                            <p>{!! nl2br(e($event->description)) !!}</p>
                        </div>
                    </div>

                    <!-- Map / Meeting Link Section -->
                    @if($event->location_type === 'physical')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 md:p-8 border-b border-gray-100">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>
                                    Venue Location
                                </h2>
                            </div>
                            <div class="bg-gray-200 h-64 w-full flex items-center justify-center">
                                @if($event->address)
                                    <!-- Fixed Map Iframe to work in all browsers -->
                                    <iframe src="https://maps.google.com/maps?q={{ urlencode($event->address) }}&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                @elseif($event->map_link)
                                    <iframe src="{{ $event->map_link }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                @else
                                    <div class="text-center text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                        <p class="font-medium text-sm">{{ $event->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($event->location_type === 'online')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>
                                Joining Online
                            </h2>
                            <div class="text-gray-600 space-y-2 text-sm md:text-base">
                                <p>This event will be hosted online. Use the details below to join us on the day.</p>
                                @if($event->meeting_link)
                                    <p><strong>Link:</strong> <a href="{{ $event->meeting_link }}" target="_blank" class="text-primary-600 hover:underline">{{ $event->meeting_link }}</a></p>
                                @endif
                                @if($event->passcode)
                                    <p><strong>Passcode:</strong> {{ $event->passcode }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right Column: Registration Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">

                        @php
                            $isPast = $event->end_date ? Carbon::parse($event->end_date) < Carbon::now() : Carbon::parse($event->start_date) < Carbon::now();
                        @endphp

                        <!-- Register Card -->
                        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">

                            @if($isPast)
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">Event Concluded</h3>
                                    <p class="text-sm text-gray-500">This event has already ended.</p>
                                </div>
                            @else
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Register to Attend</h3>
                                <p class="text-sm text-gray-500 mb-6">Free to attend for all parent carers.</p>

                                @if($isReserved)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center mb-6">
                                        <p class="text-green-700 font-medium text-sm">You're registered!</p>
                                        <p class="text-green-600 text-xs mt-1">We look forward to seeing you there.</p>
                                    </div>
                                @else
                                    @if(auth()->check())
                                        <!-- Huge Button for Logged-in Users -->
                                        <button wire:click="reserveSpot" class="block w-full bg-orange-500 text-white text-center font-medium py-4 rounded-lg hover:bg-orange-600 transition mb-6 text-lg shadow-md">
                                            Reserve My Spot
                                        </button>
                                    @else
                                        <!-- Email Input Section for Guests -->
                                        <div class="mb-6">
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Want to find out more?</label>
                                            <p class="text-xs text-gray-500 mb-2">Enter your email to keep in touch and get updates about this event and future community news.</p>
                                            <input type="email" id="email" wire:model="email" placeholder="Enter your email address" class="block w-full px-3 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <button wire:click="reserveSpot" class="block w-full bg-orange-500 text-white text-center font-medium py-3.5 rounded-lg hover:bg-orange-600 transition mb-6">
                                            Reserve My Spot
                                        </button>
                                    @endif
                                @endif

                                <!-- Online Event Link Section -->
                                @if($event->location_type === 'online')
                                    <div class="mb-6 bg-blue-50 border border-blue-100 p-4 rounded-lg">
                                        <p class="font-semibold text-blue-900 mb-2 text-sm">Online Event Access</p>
                                        @if($event->meeting_link)
                                            <a href="{{ $event->meeting_link }}" target="_blank" class="block w-full bg-blue-600 text-white text-center font-medium py-2.5 rounded-lg hover:bg-blue-700 transition mb-2">
                                                Join Meeting Room
                                            </a>
                                        @endif
                                        @if($event->passcode)
                                            <p class="text-sm text-blue-800 text-center mt-2"><strong>Passcode:</strong> {{ $event->passcode }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Quick Details List -->
                                <div class="mt-6 pt-6 border-t border-gray-100 space-y-4 text-sm">
                                    <div class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Date & Time</p>
                                            <p class="text-gray-500">{{ $event->start_date->format('F d, Y') }}</p>
                                            <p class="text-gray-500">{{ $event->start_date->format('g:i A') }} {{ $event->end_date ? '- ' . $event->end_date->format('g:i A') : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Location</p>
                                            @if($event->location_type === 'physical')
                                                <p class="text-gray-500">{{ $event->address }}</p>
                                            @else
                                                <p class="text-gray-500">Online Event</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Who Can Attend</p>
                                            <p class="text-gray-500">Parent carers of children with SEND (0-25 years)</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Share Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Share this event</h3>
                            <div class="flex space-x-3">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($eventUrl) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 border border-gray-100 text-gray-500 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-100 transition">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                                </a>
                                <!-- Twitter / X -->
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode($eventUrl) }}&text={{ urlencode($event->title) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 border border-gray-100 text-gray-500 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-100 transition">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                                </a>
                                <!-- WhatsApp -->
                                <a href="https://wa.me/?text={{ urlencode($event->title . ' - ' . $eventUrl) }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 border border-gray-100 text-gray-500 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-100 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                </a>
                                <!-- Email -->
                                <a href="mailto:?subject={{ urlencode($event->title) }}&body={{ urlencode('Check out this event: ' . $eventUrl) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 border border-gray-100 text-gray-500 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-100 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</div>