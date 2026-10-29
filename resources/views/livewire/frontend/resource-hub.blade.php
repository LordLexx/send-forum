<?php
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    public function with(): array
    {
        $documents = Resource::where('status', 'published')->where('type', 'file')->latest()->get();
        $links = Resource::where('status', 'published')->where('type', 'link')->latest()->get();

        $nextEvent = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->first();

        return compact('documents', 'links', 'nextEvent');
    }
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Knowledge & Tools</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">SEND Resource Hub</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Practical information, helpful tools, and trusted guidance created by parents, for parents.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">

                <!-- ===== LEFT & MAIN CONTENT: Resources ===== -->
                <main class="lg:col-span-2 space-y-12">

                    @if($documents->isNotEmpty())
                    <!-- Section 1: Documents & Toolkits -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span> Documents & Toolkits
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

                                <!-- Document Card -->
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                    <div class="relative h-40 {{ $bgColor }} flex items-center justify-center overflow-hidden">
                                        @if($isImage)
                                            <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover transition duration-300">
                                        @else
                                            <svg class="h-16 w-16 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        @endif
                                        <span class="absolute top-3 right-3 {{ $badgeClass }} text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                                        @if($doc->is_fathers_hub)
                                            <span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm flex items-center">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                                Fathers Hub
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-5 flex flex-col flex-grow">
                                        <h3 class="font-bold text-gray-900 text-base mb-1 transition">{{ $doc->title }}</h3>
                                        <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $doc->description }}</p>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-full bg-primary-50 text-primary-700 text-center text-sm font-medium py-2 rounded-lg hover:bg-primary-100 transition flex items-center justify-center">
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
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-1 bg-orange-500 rounded-full mr-3"></span> Useful Links & Videos
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
                                        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                        elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                        if ($videoId) $thumbnailUrl = 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
                                        $badgeText = 'YOUTUBE';
                                        $badgeClass = 'bg-red-600 text-white';
                                    } elseif ($isVimeo) {
                                        $videoId = '';
                                        if (preg_match('/vimeo\.com\/([0-9]+)/', $link->external_url, $matches)) $videoId = $matches[1];
                                        if ($videoId) $thumbnailUrl = 'https://vumbnail.com/' . $videoId . '.jpg';
                                        $badgeText = 'VIMEO';
                                        $badgeClass = 'bg-blue-600 text-white';
                                    }
                                @endphp

                                <!-- Link Card -->
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                    <div class="relative h-40 bg-gray-900 overflow-hidden">
                                        @if($thumbnailUrl)
                                            <img src="{{ $thumbnailUrl }}" alt="{{ $link->title }}" class="w-full h-full object-cover transition duration-300 {{ $isVideo ? 'opacity-80' : 'opacity-100' }}" onerror="this.style.display='none'; document.getElementById('pub-fallback-{{ $link->id }}').classList.remove('hidden');">
                                            <div id="pub-fallback-{{ $link->id }}" class="hidden absolute inset-0 gradient-bg flex items-center justify-center">
                                                <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                            </div>
                                        @else
                                            <div class="w-full h-full gradient-bg flex items-center justify-center">
                                                <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                            </div>
                                        @endif

                                        @if($link->is_fathers_hub)
                                            <span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm z-10 flex items-center">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                                Fathers Hub
                                            </span>
                                        @endif

                                        @if($isVideo)
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="h-6 w-6 text-gray-900 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                            </div>
                                        </div>
                                        @endif

                                        <span class="absolute top-3 right-3 {{ $badgeClass }} text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                                    </div>
                                    <div class="p-5 flex flex-col flex-grow">
                                        <h3 class="font-bold text-gray-900 text-base mb-1 transition">{{ $link->title }}</h3>
                                        <p class="text-gray-500 text-xs mb-4 flex-grow">{{ $link->description }}</p>
                                        <a href="{{ $link->external_url }}" target="_blank" rel="noopener noreferrer" class="w-full bg-primary-50 text-primary-700 text-center text-sm font-medium py-2 rounded-lg hover:bg-primary-100 transition flex items-center justify-center">
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
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <h3 class="font-bold text-gray-900 mb-1">No resources published yet</h3>
                            <p class="text-sm">Please check back soon for helpful toolkits and links!</p>
                        </div>
                    @endif

                </main>

                <!-- ===== RIGHT SIDEBAR ===== -->
                <aside class="hidden lg:block lg:col-span-1">
                    <div class="space-y-6 sticky top-24">

                        <!-- Next Event Widget -->
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
                                    <a href="{{ route('events.show', $nextEvent) }}" wire:navigate class="block mt-3 text-center bg-primary-50 text-primary-700 text-sm font-medium py-2 rounded-lg hover:bg-primary-100 transition">View Details</a>
                                </div>
                            </div>
                        @endif

                        <!-- Help Card -->
                        <div class="bg-gray-900 text-white p-6 rounded-2xl">
                            <h3 class="font-bold text-lg mb-2">Need More Help?</h3>
                            <p class="text-gray-300 text-sm mb-4">If you can't find what you're looking for, get in touch with our team.</p>
                            <a href="{{ route('contact') }}" wire:navigate class="block w-full text-center bg-orange-500 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-orange-600 transition text-sm">
                                Contact Us
                            </a>
                        </div>

                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
