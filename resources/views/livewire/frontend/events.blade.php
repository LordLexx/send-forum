<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Event;
use Carbon\Carbon;

new #[Layout('layouts.master')] class extends Component {
    // Public property to hold the selected category
    public string $category = 'all';

    // The with() function runs on load and every time $category changes
    public function with(): array {
        $now = Carbon::now();

        $baseQuery = Event::where('status', 'published');

        // Apply category filter if not 'all'
        if ($this->category !== 'all') {
            $baseQuery->where('category', $this->category);
        }

        // Events that are currently happening (started but haven't ended)
        $currentEvents = (clone $baseQuery)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('start_date', 'asc')
            ->get();

        // Events starting in the future
        $upcomingEvents = (clone $baseQuery)
            ->where('start_date', '>', $now)
            ->orderBy('start_date', 'asc')
            ->get();

        // Events that have already ended
        $pastEvents = (clone $baseQuery)
            ->where('end_date', '<', $now)
            ->orderBy('start_date', 'desc')
            ->get();

        return compact('currentEvents', 'upcomingEvents', 'pastEvents');
    }
};
?>

<div>
    @php
        // Map categories to badge colors inside the view
        $catColors = [
            'Drop-in' => 'bg-primary-600/90',
            'Workshop' => 'bg-purple-600/90',
            'Family Fun' => 'bg-orange-600/90',
            'Online Webinar' => 'bg-blue-600/90'
        ];

        $fathersBadge = '<span class="absolute top-4 left-4 z-10 bg-emerald-600 text-white text-xs px-3 py-1 rounded-full font-medium backdrop-blur-sm flex items-center">
            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            Fathers Hub
        </span>';

        // Helper to determine button classes based on active state
        $buttonClass = fn($cat) => $category === $cat
            ? 'bg-primary-600 text-white px-5 py-2 rounded-full text-sm font-medium shadow-sm shrink-0'
            : 'bg-white text-gray-600 px-5 py-2 rounded-full text-sm font-medium hover:bg-primary-50 border border-gray-200 transition shrink-0';
    @endphp

    <!-- Custom Style to hide scrollbar for a cleaner slider look -->
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1496024840928-4c417adf211d?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Connect & Learn</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Events & Workshops</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Join us at our parent drop-ins, informative workshops, and family events. Share experiences and shape local services together.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter Buttons (Functional & Scrollable) -->
            <div class="mb-16">
                <div class="flex justify-start md:justify-center gap-3 overflow-x-auto pb-2 whitespace-nowrap scrollbar-hide">
                    <button wire:click="$set('category', 'all')" class="{{ $buttonClass('all') }}">All Events</button>
                    <button wire:click="$set('category', 'Drop-in')" class="{{ $buttonClass('Drop-in') }}">Drop-ins</button>
                    <button wire:click="$set('category', 'Workshop')" class="{{ $buttonClass('Workshop') }}">Workshops</button>
                    <button wire:click="$set('category', 'Family Fun')" class="{{ $buttonClass('Family Fun') }}">Family Fun</button>
                    <button wire:click="$set('category', 'Online Webinar')" class="{{ $buttonClass('Online Webinar') }}">Online Webinars</button>
                </div>
            </div>

            <!-- ===== CURRENT EVENTS (HAPPENING NOW) ===== -->
            @if($currentEvents->isNotEmpty())
            <div class="mb-24">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="relative flex h-3 w-3 mr-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                    </span>
                    Happening Now
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($currentEvents as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg border-2 border-blue-500 flex flex-col group hover:shadow-xl transition-all duration-300">
                            <div class="relative h-52 overflow-hidden">
                                @if($event->is_fathers_hub)
                                    {!! $fathersBadge !!}
                                @endif
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                                <!-- Live Badge -->
                                @if($event->is_fathers_hub)
                                    <span class="absolute top-4 left-20 z-10 bg-emerald-600 text-white text-xs px-3 py-1 rounded-full font-medium backdrop-blur-sm flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                        Fathers Hub
                                    </span>
                                @endif
                                <div class="absolute top-4 left-4 flex items-center space-x-2 bg-white/95 backdrop-blur-sm rounded-xl p-2 shadow-md text-center">
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                                    </span>
                                    <span class="text-red-600 font-bold text-sm leading-none uppercase">Live</span>
                                </div>

                                <span class="absolute bottom-4 right-4 {{ $catColors[$event->category] ?? 'bg-primary-600/90' }} text-white text-xs px-3 py-1 rounded-full font-medium backdrop-blur-sm">{{ $event->category }}</span>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4 flex-grow">{{ \Illuminate\Support\Str::limit($event->description, 120) }}</p>
                                <div class="space-y-2 text-sm text-gray-500 mb-5">
                                    <p class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $event->start_date->format('g:i A') }} - {{ $event->end_date ? $event->end_date->format('g:i A') : '' }}
                                    </p>
                                    <p class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ $event->location_type === 'physical' ? $event->address : 'Online via Zoom' }}
                                    </p>
                                </div>
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full bg-blue-50 text-blue-700 text-center font-medium py-2.5 rounded-lg hover:bg-blue-100 transition">Join Event Now</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ===== UPCOMING EVENTS ===== -->
            <div class="mb-24">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-1 bg-orange-500 rounded-full mr-4"></span>
                    Upcoming Events
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($upcomingEvents as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col group hover:shadow-lg transition-all duration-300">
                            <div class="relative h-52 overflow-hidden">
                                @if($event->is_fathers_hub)
                                    {!! $fathersBadge !!}
                                @endif
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <!-- Date Badge -->
                                <div class="absolute top-4 left-4 bg-white rounded-xl p-2 shadow-md text-center w-16">
                                    <span class="block text-orange-600 font-bold text-sm leading-none uppercase">{{ $event->start_date->format('M') }}</span>
                                    <span class="block text-gray-900 font-bold text-2xl leading-none mt-1">{{ $event->start_date->format('d') }}</span>
                                </div>
                                <span class="absolute bottom-4 right-4 {{ $catColors[$event->category] ?? 'bg-primary-600/90' }} text-white text-xs px-3 py-1 rounded-full font-medium backdrop-blur-sm">{{ $event->category }}</span>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4 flex-grow">{{ \Illuminate\Support\Str::limit($event->description, 120) }}</p>
                                <div class="space-y-2 text-sm text-gray-500 mb-5">
                                    <p class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $event->start_date->format('g:i A') }} - {{ $event->end_date ? $event->end_date->format('g:i A') : '' }}
                                    </p>
                                    <p class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ $event->location_type === 'physical' ? $event->address : 'Online via Zoom' }}
                                    </p>
                                </div>
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full bg-primary-50 text-primary-700 text-center font-medium py-2.5 rounded-lg hover:bg-primary-100 transition">View Event</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center py-8">No upcoming events scheduled right now. Please check back soon!</p>
                    @endforelse
                </div>
            </div>

            <!-- ===== PAST EVENTS ===== -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-1 bg-gray-300 rounded-full mr-4"></span>
                    Past Events
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($pastEvents as $event)
                        <div class="past-event bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col group hover:shadow-lg transition-all duration-300">
                            <div class="relative h-44 overflow-hidden">
                                @if($event->is_fathers_hub)
                                    {!! $fathersBadge !!}
                                @endif
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1573164713988-8665fc963095?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                    <span class="text-white text-xs px-3 py-1 bg-gray-900/50 rounded-full font-medium uppercase tracking-wider">Completed</span>
                                </div>
                            </div>
                            <div class="p-5 flex-grow flex flex-col">
                                <p class="text-xs text-gray-400 font-medium mb-1">{{ $event->start_date->format('F Y') }}</p>
                                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-500 text-sm mb-4 flex-grow">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="text-primary-600 hover:text-primary-800 font-medium text-sm inline-flex items-center">
                                    View Details
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center py-8">No past events to display.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </section>
</div>
