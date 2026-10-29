<?php
use App\Models\Post;
use App\Models\Category;
use App\Models\Event;
use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.forum-layout')] class extends Component {
    public $categories; // All active categories (for modals)
    public $feedCategories; // Only priority categories (for sidebar)
    public $nextEvent;

    public function mount(): void
    {
        $this->categories = Category::where('status', 'active')->withCount('posts')->orderBy('name')->get();

        // ONLY priority categories for the feed sidebar
        $this->feedCategories = Category::where('status', 'active')
            ->where('make_priority', true)
            ->withCount('posts')
            ->orderBy('name')
            ->get();

        $this->nextEvent = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->first();
    }

    public function render(): mixed
    {
        $documents = Resource::where('status', 'published')->where('type', 'file')->latest()->get();
        $links = Resource::where('status', 'published')->where('type', 'link')->latest()->get();

        return view('livewire.frontend.resources', [
            'documents' => $documents,
            'links' => $links,
            'categories' => $this->categories,
            'feedCategories' => $this->feedCategories,
            'nextEvent' => $this->nextEvent
        ]);
    }
}; ?>

@section('title', 'Resource Hub · Slough SEND PCF')

<style>
    .resource-card:hover .resource-title { color: #141f52; }
    .resource-card:hover .card-thumbnail img { transform: scale(1.05); }
</style>

<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header Banner -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center space-x-4 text-center md:text-left">
                <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center text-3xl border border-primary-100 shrink-0">📚</div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">SEND Resource Hub</h1>
                    <p class="text-gray-500 mt-1 text-sm max-w-xl">Practical information, helpful tools, and trusted guidance created by parents, for parents.</p>
                </div>
            </div>
        </div>

        <!-- ===== MOBILE QUICK LINKS (Mobile Only) ===== -->
        <div class="lg:hidden mb-6 grid grid-cols-3 gap-2">
            <a href="{{ route('events.index') }}" class="flex flex-col items-center justify-center py-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <svg class="h-5 w-5 text-primary-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span class="text-xs text-gray-700 font-medium">Events</span>
            </a>
            <a href="{{ route('saved.posts') }}" class="flex flex-col items-center justify-center py-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <svg class="h-5 w-5 text-primary-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                <span class="text-xs text-gray-700 font-medium">Saved</span>
            </a>
            <a href="{{ route('resources') }}" class="flex flex-col items-center justify-center py-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <svg class="h-5 w-5 text-primary-600 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span class="text-xs text-gray-700 font-medium">Resources</span>
            </a>
        </div>

        <!-- ===== MOBILE CATEGORIES SCROLL BAR (Mobile Only) ===== -->
        <div class="lg:hidden mb-6 min-w-0">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 -mx-4 px-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                <a href="{{ route('feed') }}" class="shrink-0 flex items-center px-4 py-2 rounded-full bg-white border border-gray-100 text-gray-700 hover:bg-gray-50 font-medium text-sm whitespace-nowrap">
                    <span class="mr-2">🏠</span> All Discussions
                </a>
                @foreach($feedCategories as $cat)
                    <a href="{{ route('category', $cat) }}" class="shrink-0 flex items-center px-4 py-2 rounded-full bg-white border border-gray-100 text-gray-700 hover:bg-gray-50 font-medium text-sm whitespace-nowrap">
                        <span class="mr-2">{{ $cat->icon }}</span> {{ $cat->name }}
                    </a>
                @endforeach

                <!-- ===== MORE CATEGORIES BUTTON (MOBILE) ===== -->
                <a href="{{ route('categories.all') }}" wire:navigate class="shrink-0 flex items-center px-4 py-2 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium text-sm whitespace-nowrap">
                    More →
                </a>
            </div>
        </div>

        <!-- ===== MOBILE NEXT EVENT (Mobile Only) ===== -->
        @if($nextEvent)
            <div class="lg:hidden mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-primary-50 px-4 py-2 border-b border-primary-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <h3 class="font-bold text-primary-800 text-sm">Next Event</h3>
                </div>
                <div class="p-4 flex items-center space-x-4">
                    <img src="{{ $nextEvent->image ? asset('storage/' . $nextEvent->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=400&auto=format&fit=crop' }}" alt="{{ $nextEvent->title }}" class="w-20 h-20 object-cover rounded-lg shrink-0">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900 text-sm line-clamp-2">{{ $nextEvent->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($nextEvent->start_date)->format('M d · h:i A') }}</p>
                        <a href="{{ route('events.show', $nextEvent) }}" wire:navigate class="inline-block mt-2 text-primary-700 text-xs font-medium hover:text-primary-500">View Details →</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- ===== LEFT SIDEBAR: Categories ===== -->
            <aside class="hidden lg:block lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto overscroll-contain pr-4 -mr-4 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Categories</h3>
                    <nav class="space-y-1">
                        <a href="{{ route('feed') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                            <span class="flex items-center"><span class="mr-3">🏠</span> All Discussions</span>
                        </a>
                        @foreach($feedCategories as $cat)
                            <a href="{{ route('category', $cat) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                                <span class="flex items-center"><span class="mr-3">{{ $cat->icon }}</span> {{ $cat->name }}</span>
                            </a>
                        @endforeach

                        <!-- ===== MORE CATEGORIES BUTTON ===== -->
                        <a href="{{ route('categories.all') }}" wire:navigate class="flex items-center justify-center gap-1 mt-3 px-3 py-2 rounded-lg text-primary-600 hover:bg-primary-50 transition text-sm font-medium border border-dashed border-primary-200">
                            More Categories
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </nav>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Quick Links</h3>
                        <nav class="space-y-1">
                            <a href="{{ route('events.index') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
                                <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Events
                            </a>
                            <a href="{{ route('saved.posts') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
                                <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                Saved Posts
                            </a>
                            <a href="{{ route('resources') }}" class="flex items-center px-3 py-2 rounded-lg bg-primary-50 text-primary-700 font-medium transition text-sm">
                                <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Resource Hub
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- ===== MAIN CONTENT: Resources ===== -->
            <main class="lg:col-span-6">

                @if($documents->isNotEmpty())
                <!-- Section 1: Documents & Toolkits -->
                <div class="mb-10">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>
                        Documents & Toolkits
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($documents as $doc)
                            @php
                                $fileExt = strtolower($doc->file_type);
                                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp']);

                                $bgColor = 'bg-gray-100';
                                $iconColor = 'text-gray-500';
                                $badgeClass = 'bg-gray-900/70 text-white';
                                $badgeText = strtoupper($fileExt);
                                $btnText = 'Download File';

                                if ($fileExt === 'pdf') {
                                    $bgColor = 'bg-red-50';
                                    $iconColor = 'text-red-500';
                                    $badgeClass = 'bg-red-600 text-white';
                                    $badgeText = 'PDF';
                                    $btnText = 'Download PDF';
                                } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                    $bgColor = 'bg-blue-50';
                                    $iconColor = 'text-blue-500';
                                    $badgeClass = 'bg-blue-600 text-white';
                                    $badgeText = 'DOCX';
                                    $btnText = 'Download Doc';
                                } elseif ($isImage) {
                                    $badgeText = strtoupper($fileExt);
                                    $btnText = 'View Image';
                                }
                            @endphp

                            <!-- Resource Card -->
                            <div class="resource-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                <div class="card-thumbnail relative h-40 {{ $bgColor }} flex items-center justify-center overflow-hidden">
                                    @if($isImage)
                                        <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover transition duration-300">
                                    @else
                                        <svg class="h-16 w-16 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    @endif
                                    <span class="absolute top-3 right-3 {{ $badgeClass }} text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                                </div>
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="resource-title font-bold text-gray-900 text-base mb-1 transition">{{ $doc->title }}</h3>
                                    <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $doc->description }}</p>
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-full bg-orange-500 text-white text-center text-sm font-medium py-2 rounded-lg hover:bg-orange-600 transition flex items-center justify-center">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        {{ $btnText }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($links->isNotEmpty())
                <!-- Section 2: Useful Links & Videos -->
                <div class="mb-10">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>
                        Useful Links & Videos
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($links as $link)
                            @php
                                $isYouTube = str_contains(strtolower($link->external_url), 'youtube.com') || str_contains(strtolower($link->external_url), 'youtu.be');
                                $isVimeo = str_contains(strtolower($link->external_url), 'vimeo.com');
                                $isVideo = $isYouTube || $isVimeo;

                                $thumbnailUrl = '';
                                $badgeText = 'LINK';
                                $badgeClass = 'bg-white/20 text-white backdrop-blur-sm';

                                if ($isYouTube) {
                                    $videoId = '';
                                    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) {
                                        $videoId = $matches[1];
                                    } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) {
                                        $videoId = $matches[1];
                                    } elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    if ($videoId) {
                                        $thumbnailUrl = 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
                                    }
                                    $badgeText = 'YOUTUBE';
                                    $badgeClass = 'bg-red-600 text-white';
                                } elseif ($isVimeo) {
                                    $videoId = '';
                                    if (preg_match('/vimeo\.com\/([0-9]+)/', $link->external_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    if ($videoId) {
                                        $thumbnailUrl = 'https://vumbnail.com/' . $videoId . '.jpg';
                                    }
                                    $badgeText = 'VIMEO';
                                    $badgeClass = 'bg-blue-600 text-white';
                                }
                            @endphp

                            <!-- Resource Card -->
                            <div class="resource-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                <div class="card-thumbnail relative h-40 bg-gray-900 overflow-hidden">
                                    @if($thumbnailUrl)
                                        <img src="{{ $thumbnailUrl }}" alt="{{ $link->title }}" class="w-full h-full object-cover transition duration-300 {{ $isVideo ? 'opacity-80' : 'opacity-100' }}" onerror="this.style.display='none'; document.getElementById('fallback-{{ $link->id }}').classList.remove('hidden');">
                                        <div id="fallback-{{ $link->id }}" class="hidden absolute inset-0 gradient-bg flex items-center justify-center">
                                            <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                        </div>
                                    @else
                                        <div class="w-full h-full gradient-bg flex items-center justify-center">
                                            <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                        </div>
                                    @endif

                                    @if($isVideo)
                                    <!-- Play Button Overlay for Videos -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                                            <svg class="h-6 w-6 text-gray-900 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                        </div>
                                    </div>
                                    @endif

                                    <span class="absolute top-3 right-3 {{ $badgeClass }} text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                                </div>
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="resource-title font-bold text-gray-900 text-base mb-1 transition">{{ $link->title }}</h3>
                                    <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $link->description }}</p>
                                    <a href="{{ $link->external_url }}" target="_blank" rel="noopener noreferrer" class="w-full bg-orange-500 text-white text-center text-sm font-medium py-2 rounded-lg hover:bg-orange-600 transition flex items-center justify-center">
                                        @if($isYouTube)
                                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Watch Video
                                        @else
                                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                            Visit Website
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($documents->isEmpty() && $links->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center text-gray-500">
                        <p>No resources have been published yet. Please check back soon!</p>
                    </div>
                @endif

            </main>

            <!-- ===== RIGHT SIDEBAR ===== -->
            <aside class="hidden lg:block lg:col-span-3">
                <div class="space-y-6 sticky top-24">

                    <!-- Upcoming Event -->
                    @if($nextEvent)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="bg-primary-50 p-4 border-b border-primary-100">
                                <h3 class="font-bold text-primary-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Next Drop-in
                                </h3>
                            </div>
                            <div class="p-4">
                                <img src="{{ $nextEvent->image ? asset('storage/' . $nextEvent->image) : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $nextEvent->title }}" class="w-full h-32 object-cover rounded-lg mb-3">
                                <p class="font-bold text-gray-900">{{ $nextEvent->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($nextEvent->start_date)->format('M d · h:i A') }}
                                </p>
                                <a href="{{ route('events.show', $nextEvent) }}" wire:navigate class="block mt-3 text-center bg-orange-500 text-white text-sm font-medium py-2 rounded-lg hover:bg-orange-600 transition">View Details</a>
                            </div>
                        </div>
                    @endif

                </div>
            </aside>
        </div>
    </div>
</div>