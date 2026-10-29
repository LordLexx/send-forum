<?php
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';
    public string $sortField = 'start_date';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
        'sortField' => ['except' => 'start_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);

        if ($event->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        $this->dispatch('notify', type: 'success', message: 'Event deleted successfully.');
    }

    public function publishEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'published']);

        $this->dispatch('notify', type: 'success', message: 'Event published successfully.');
    }

    public function with(): array
    {
        $query = Event::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%')
                    ->orWhere('meeting_link', 'like', '%' . $this->search . '%')
                    ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'upcoming') {
            $query->where('end_date', '>=', now())->where('status', '!=', 'draft');
        } elseif ($this->filter === 'drafts') {
            $query->where('status', 'draft');
        } elseif ($this->filter === 'past') {
            $query->where('end_date', '<', now());
        }
        // Single ORDER BY based on user's selected sort
        $query->orderBy($this->sortField, $this->sortDirection);

        $events = $query->paginate(10);

        return compact('events');
    }
}; ?>

@section('page_title', 'Events Management')

<div class="space-y-6">

    <!-- Controls & Filters -->
    <div
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search -->
        <div class="relative w-full md:max-w-xs">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search events..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        <!-- Filters & Actions -->
        <div class="flex items-center space-x-3 w-full md:w-auto justify-between md:justify-end">
            <!-- Sliding filter (mirrors Resources page) -->
            <div id="filter-container"
                class="flex space-x-1 bg-gray-50 p-1 rounded-full border border-gray-100 relative">
                <div id="filter-slider" wire:ignore
                    class="absolute top-1 bottom-1 rounded-full bg-white shadow-sm transition-all duration-300 ease-out">
                </div>
                <button data-filter="all" wire:click="$set('filter', 'all')"
                    class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'all' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                <button data-filter="upcoming" wire:click="$set('filter', 'upcoming')"
                    class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'upcoming' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Upcoming</button>
                <button data-filter="drafts" wire:click="$set('filter', 'drafts')"
                    class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'drafts' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Drafts</button>
                <button data-filter="past" wire:click="$set('filter', 'past')"
                    class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'past' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Past</button>
            </div>
            <a href="{{ route('admin.events.create') }}"
                class="flex items-center bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Event
            </a>
        </div>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Event Details (Sortable) -->
                        <th scope="col" wire:click="sortBy('title')"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Event Details</span>
                                @if($sortField === 'title' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                @elseif($sortField === 'title' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <!-- Date & Time (Sortable) -->
                        <th scope="col" wire:click="sortBy('start_date')"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Date & Time</span>
                                @if($sortField === 'start_date' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                @elseif($sortField === 'start_date' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <!-- Location/Link (Sortable) -->
                        <th scope="col" wire:click="sortBy('location_type')"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Location/Link</span>
                                @if($sortField === 'location_type' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                @elseif($sortField === 'location_type' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <!-- Status (Sortable) -->
                        <th scope="col" wire:click="sortBy('status')"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Status</span>
                                @if($sortField === 'status' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                @elseif($sortField === 'status' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse($events as $event)
                        @php
                            $start = \Carbon\Carbon::parse($event->start_date);
                            $end = \Carbon\Carbon::parse($event->end_date);

                            $isPast = $end < now();
                            $isLive = !$isPast && $start <= now();

                            $statusText = ucfirst($event->status);
                            $statusColor = 'bg-gray-100 text-gray-500';
                            $rowOpacity = '';

                            if ($event->status === 'draft') {
                                $statusColor = 'bg-yellow-100 text-yellow-700';
                            } elseif ($event->status === 'cancelled') {
                                $statusColor = 'bg-red-100 text-red-700';
                                $statusText = 'Cancelled';
                            } elseif ($isPast) {
                                $statusColor = 'bg-gray-100 text-gray-500';
                                $statusText = 'Past';
                                $rowOpacity = 'opacity-60';
                            } elseif ($isLive) {
                                $statusColor = 'bg-blue-100 text-blue-700';
                                $statusText = 'Happening Now';
                            } else {
                                $statusColor = 'bg-green-100 text-green-700';
                                $statusText = 'Upcoming';
                            }
                        @endphp

                        <tr wire:key="event-{{ $event->id }}"
                            class="hover:bg-gray-50 transition {{ $rowOpacity }} {{ $event->status === 'draft' ? 'bg-yellow-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">

                                    @if ($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                                            class="h-12 w-12 rounded-lg object-cover mr-4 shrink-0">
                                    @else
                                        <div
                                            class="h-12 w-12 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mr-4 shrink-0">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500">Category: {{ $event->category }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $event->start_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $event->start_date->format('g:i A') }}
                                    @if($event->end_date)
                                        - {{ $event->end_date->format('g:i A') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="max-w-[200px] truncate">
                                    @if ($event->location_type === 'physical')
                                        {{ $event->address ?: 'No address set' }}
                                    @elseif ($event->location_type === 'online')
                                        <span class="text-primary-600">Online Event</span>
                                    @else
                                        No location set
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    @if($statusText === 'Happening Now')
                                        <span class="w-2 h-2 mr-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                    @endif
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.events.guests', $event) }}" wire:navigate
                                        class="inline-flex items-center px-2.5 py-1.5 border border-blue-200 text-blue-700 bg-blue-50 rounded-md text-xs font-medium hover:bg-blue-100 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Guests
                                    </a>
                                    <a href="{{ route('admin.events.edit', $event) }}" wire:navigate
                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 text-gray-600 bg-white rounded-md text-xs font-medium hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    @if($event->status === 'draft')
                                        <button wire:click="publishEvent({{ $event->id }})"
                                            wire:confirm="Publish this event to make it visible to users?"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-primary-200 text-primary-700 bg-primary-50 rounded-md text-xs font-medium hover:bg-primary-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Publish
                                        </button>
                                    @endif

                                    <button wire:click="deleteEvent({{ $event->id }})"
                                        wire:confirm="Are you sure you want to delete this event?"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">No events found</p>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <script>
        // Sliding filter — same logic as Resources page
        function alignFilterSlider() {
            const slider = document.getElementById('filter-slider');
            if (!slider) return;

            const activeBtn = document.querySelector('#filter-container button.text-gray-900');
            if (activeBtn) {
                slider.style.left = activeBtn.offsetLeft + 'px';
                slider.style.width = activeBtn.offsetWidth + 'px';
            }
        }

        // Run on initial load
        alignFilterSlider();

        // Re-align on window resize
        window.addEventListener('resize', alignFilterSlider);

        // Instantly move slider on click, BEFORE Livewire finishes loading
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#filter-container button');
            if (btn) {
                const slider = document.getElementById('filter-slider');
                if (slider) {
                    slider.style.left = btn.offsetLeft + 'px';
                    slider.style.width = btn.offsetWidth + 'px';
                }
            }
        });

        // Re-align after Livewire navigates (wire:navigate swaps content)
        document.addEventListener('livewire:navigated', alignFilterSlider);
    </script>
</div>