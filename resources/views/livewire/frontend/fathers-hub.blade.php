<?php
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Carbon\Carbon;

new #[Layout('layouts.master')] class extends Component {
    public function with(): array
    {
        $now = Carbon::now();

        // Fathers Hub events: split into upcoming + past for clarity
        $upcomingEvents = Event::where('status', 'published')
            ->where('is_fathers_hub', true)
            ->where('start_date', '>=', $now)
            ->orderBy('start_date', 'asc')
            ->get();

        $pastEvents = Event::where('status', 'published')
            ->where('is_fathers_hub', true)
            ->where('end_date', '<', $now)
            ->orderBy('start_date', 'desc')
            ->limit(6)
            ->get();

        // Fathers Hub resources
        $documents = Resource::where('status', 'published')
            ->where('is_fathers_hub', true)
            ->where('type', 'file')
            ->latest()
            ->get();

        $links = Resource::where('status', 'published')
            ->where('is_fathers_hub', true)
            ->where('type', 'link')
            ->latest()
            ->get();

        return compact('upcomingEvents', 'pastEvents', 'documents', 'links');
    }
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 z-0" style="background: linear-gradient(135deg, #065f46 0%, #047857 50%, #0d9488 100%);"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1519689680058-324335c77eba?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-flex items-center bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                For Dads & Male Carers
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Fathers Hub</h1>
            <p class="text-lg text-emerald-50 max-w-2xl mx-auto">A dedicated space for fathers and male carers of children with SEND — events, resources, and a community that gets it.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Intro Block -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 mb-16">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">You belong here</h2>
                    <p class="text-gray-600 leading-relaxed">The Fathers Hub brings together the events, resources, and conversations specifically relevant to dads and male carers navigating the SEND journey. Whether you're looking for practical advice, a chance to connect with other fathers, or just a space to be heard — this is your corner of the forum.</p>
                </div>
            </div>

            <!-- ===== UPCOMING FATHERS HUB EVENTS ===== -->
            @if($upcomingEvents->isNotEmpty())
            <div class="mb-24">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-1 bg-emerald-500 rounded-full mr-4"></span>
                    Upcoming Events
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($upcomingEvents as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col group hover:shadow-lg transition-all duration-300">
                            <div class="relative h-52 overflow-hidden">
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute top-4 left-4 bg-white rounded-xl p-2 shadow-md text-center w-16">
                                    <span class="block text-emerald-600 font-bold text-sm leading-none uppercase">{{ $event->start_date->format('M') }}</span>
                                    <span class="block text-gray-900 font-bold text-2xl leading-none mt-1">{{ $event->start_date->format('d') }}</span>
                                </div>
                                <span class="absolute top-4 right-4 bg-emerald-600 text-white text-xs px-3 py-1 rounded-full font-medium backdrop-blur-sm flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                    Fathers Hub
                                </span>
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
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full bg-emerald-50 text-emerald-700 text-center font-medium py-2.5 rounded-lg hover:bg-emerald-100 transition">View Event</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ===== FATHERS HUB RESOURCES ===== -->
            @if($documents->isNotEmpty() || $links->isNotEmpty())
            <div class="mb-24">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-1 bg-emerald-500 rounded-full mr-4"></span>
                    Resources for Fathers
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($documents as $doc)
                        @php
                            $fileExt = strtolower($doc->file_type);
                            $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp']);
                            $bgColor = 'bg-gray-100';
                            $iconColor = 'text-gray-500';
                            $badgeText = strtoupper($fileExt);
                            $btnText = 'Download File';

                            if ($fileExt === 'pdf') {
                                $bgColor = 'bg-red-50'; $iconColor = 'text-red-500'; $badgeText = 'PDF'; $btnText = 'Download PDF';
                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                $bgColor = 'bg-blue-50'; $iconColor = 'text-blue-500'; $badgeText = 'DOCX'; $btnText = 'Download Doc';
                            } elseif ($isImage) {
                                $badgeText = strtoupper($fileExt); $btnText = 'View Image';
                            }
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                            <div class="relative h-40 {{ $bgColor }} flex items-center justify-center overflow-hidden">
                                @if($isImage)
                                    <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="h-16 w-16 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                @endif
                                <span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                    Fathers Hub
                                </span>
                                <span class="absolute top-3 right-3 bg-gray-900/70 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                            </div>
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="font-bold text-gray-900 text-base mb-1">{{ $doc->title }}</h3>
                                <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $doc->description }}</p>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-full bg-emerald-50 text-emerald-700 text-center text-sm font-medium py-2 rounded-lg hover:bg-emerald-100 transition flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ $btnText }}
                                </a>
                            </div>
                        </div>
                    @endforeach

                    @foreach($links as $link)
                        @php
                            $isYouTube = str_contains(strtolower($link->external_url), 'youtube.com') || str_contains(strtolower($link->external_url), 'youtu.be');
                            $isVimeo = str_contains(strtolower($link->external_url), 'vimeo.com');
                            $isVideo = $isYouTube || $isVimeo;
                            $thumbnailUrl = '';
                            $badgeText = 'LINK';

                            if ($isYouTube) {
                                $videoId = '';
                                if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                if ($videoId) $thumbnailUrl = 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
                                $badgeText = 'YOUTUBE';
                            } elseif ($isVimeo) {
                                $videoId = '';
                                if (preg_match('/vimeo\.com\/([0-9]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                if ($videoId) $thumbnailUrl = 'https://vumbnail.com/' . $videoId . '.jpg';
                                $badgeText = 'VIMEO';
                            }
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                            <div class="relative h-40 bg-gray-900 overflow-hidden">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $link->title }}" class="w-full h-full object-cover {{ $isVideo ? 'opacity-80' : 'opacity-100' }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, #065f46 0%, #0d9488 100%);">
                                        <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                    </div>
                                @endif
                                <span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                    Fathers Hub
                                </span>
                                <span class="absolute top-3 right-3 bg-white/20 text-white backdrop-blur-sm text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                            </div>
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="font-bold text-gray-900 text-base mb-1">{{ $link->title }}</h3>
                                <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $link->description }}</p>
                                <a href="{{ $link->external_url }}" target="_blank" rel="noopener noreferrer" class="w-full bg-emerald-50 text-emerald-700 text-center text-sm font-medium py-2 rounded-lg hover:bg-emerald-100 transition flex items-center justify-center">
                                    @if($isYouTube)
                                        Watch Video
                                    @else
                                        Visit Website
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ===== PAST FATHERS HUB EVENTS ===== -->
            @if($pastEvents->isNotEmpty())
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-1 bg-gray-300 rounded-full mr-4"></span>
                    Past Fathers Hub Events
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($pastEvents as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col group hover:shadow-lg transition-all duration-300">
                            <div class="relative h-44 overflow-hidden">
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1573164713988-8665fc963095?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                    <span class="text-white text-xs px-3 py-1 bg-gray-900/50 rounded-full font-medium uppercase tracking-wider">Completed</span>
                                </div>
                            </div>
                            <div class="p-5 flex-grow flex flex-col">
                                <p class="text-xs text-gray-400 font-medium mb-1">{{ $event->start_date->format('F Y') }}</p>
                                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $event->title }}</h3>
                                <p class="text-gray-500 text-sm mb-4 flex-grow">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="text-emerald-600 hover:text-emerald-800 font-medium text-sm inline-flex items-center">
                                    View Details
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Empty state -->
            @if($upcomingEvents->isEmpty() && $pastEvents->isEmpty() && $documents->isEmpty() && $links->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-emerald-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    <h3 class="font-bold text-gray-900 mb-1">Nothing here just yet</h3>
                    <p class="text-sm">We're building out the Fathers Hub. Check back soon for events and resources tailored for dads and male carers.</p>
                </div>
            @endif

        </div>
    </section>
</div>
