<?php
use App\Models\Post;
use App\Models\PostReport;
use App\Models\Comment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin-layout')] class extends Component {
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post->load(['user', 'category', 'comments.user', 'comments.replies.user', 'reports', 'saves']);
    }

    public function hidePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            // SECURITY: Moderators cannot manage staff posts
            if (auth()->user()->isModerator() && $post->user->isStaff()) {
                $this->dispatch('notify', type: 'error', message: "You cannot manage posts created by staff members.");
                return;
            }
            $post->update(['status' => 'hidden']);
            $this->post = $this->post->fresh();
            $this->dispatch('notify', type: 'success', message: "Post hidden successfully.");
        }
    }

    public function restorePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            // SECURITY: Moderators cannot manage staff posts
            if (auth()->user()->isModerator() && $post->user->isStaff()) {
                $this->dispatch('notify', type: 'error', message: "You cannot manage posts created by staff members.");
                return;
            }
            $post->update(['status' => 'published']);
            $this->post = $this->post->fresh();
            $this->dispatch('notify', type: 'success', message: "Post restored successfully.");
        }
    }

    public function deletePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            // SECURITY: Moderators cannot manage staff posts
            if (auth()->user()->isModerator() && $post->user->isStaff()) {
                $this->dispatch('notify', type: 'error', message: "You cannot manage posts created by staff members.");
                return;
            }
            if (!empty($post->media)) {
                foreach ($post->media as $mediaPath) {
                    Storage::disk('public')->delete($mediaPath);
                }
            }
            $post->delete();
            $this->dispatch('notify', type: 'error', message: "Post deleted permanently.");
            $this->redirect(route('admin.posts'), navigate: true);
        }
    }

    public function dismissReport($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            // SECURITY: Moderators cannot manage staff posts
            if (auth()->user()->isModerator() && $post->user->isStaff()) {
                $this->dispatch('notify', type: 'error', message: "You cannot manage posts created by staff members.");
                return;
            }
            PostReport::where('post_id', $postId)->delete();
            $this->post = $this->post->fresh(['reports']);
            $this->dispatch('notify', type: 'success', message: "Reports dismissed for post.");
        }
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment) {
            // SECURITY: Moderators cannot manage comments on staff posts
            if (auth()->user()->isModerator() && $comment->post->user->isStaff()) {
                $this->dispatch('notify', type: 'error', message: "You cannot manage comments on posts created by staff members.");
                return;
            }
            $post = $comment->post;
            $decrementAmount = 1 + $comment->replies->count();

            $comment->delete();

            if ($post) {
                $post->decrement('replies_count', $decrementAmount);
            }

            $this->post = $this->post->fresh(['user', 'category', 'comments.user', 'comments.replies.user', 'reports', 'saves']);
            $this->dispatch('notify', type: 'success', message: "Comment deleted successfully.");
        }
    }
}; ?>

@section('page_title', 'Viewing Post · Moderation')

<div x-data="{
    showSlideshow: false,
    currentSlide: 0,
    slides: [],
    openSlideshow(items, index) {
        this.slides = items;
        this.currentSlide = index;
        this.showSlideshow = true;
    },
    pauseCurrentVideo() {
        const videos = document.querySelectorAll('.slideshow-video');
        videos.forEach(v => { if (!v.paused) v.pause(); });
    },
    nextSlide() {
        this.pauseCurrentVideo();
        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    },
    prevSlide() {
        this.pauseCurrentVideo();
        this.currentSlide = this.currentSlide === 0 ? this.slides.length - 1 : this.currentSlide - 1;
    },
    closeSlideshow() {
        this.pauseCurrentVideo();
        this.showSlideshow = false;
    }
}">

    <!-- Back Button & Moderation Actions -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <a href="{{ route('admin.posts') }}" wire:navigate class="flex items-center text-gray-600 hover:text-primary-600 transition">
            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Posts
        </a>
        <div class="flex items-center space-x-2">
            {{-- Security: Hide action buttons if moderator is viewing a staff member's post --}}
            @if(!auth()->user()->isModerator() || !$post->user->isStaff())
                @if($post->reports->isNotEmpty())
                    <button wire:click="dismissReport({{ $post->id }})" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Dismiss Reports ({{ $post->reports->count() }})</button>
                @endif
                @if($post->status == 'published')
                    <button wire:click="hidePost({{ $post->id }})" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">Hide Post</button>
                @else
                    <button wire:click="restorePost({{ $post->id }})" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Restore Post</button>
                @endif
                <button wire:click="deletePost({{ $post->id }})" wire:confirm="Are you sure you want to permanently delete this post?" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">Delete Permanently</button>
            @endif
        </div>
    </div>

    <!-- Post Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="mr-4 shrink-0">
                @if ($post->user && $post->user->profile_photo_path)
                    <img src="{{ Storage::url($post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-lg">
                        {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                @if($post->user)
                    <a href="{{ route('admin.members.show', $post->user) }}" wire:navigate class="font-semibold text-gray-900 hover:text-primary-600 transition">{{ $post->user->name }}</a>
                @else
                    <p class="font-semibold text-gray-900">Unknown User</p>
                @endif
                <p class="text-xs text-gray-500">Posted {{ $post->created_at->diffForHumans() }} · {{ $post->hugs_count }} Hugs · {{ $post->saves->count() }} Saves</p>
            </div>
            <div class="ml-auto text-right">
                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-3 py-1 rounded-full">{{ $post->category->icon ?? '📋' }} {{ $post->category->name ?? 'N/A' }}</span>
                @if($post->reports->isNotEmpty())
                    <span class="block mt-2 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full">⚠️ Reported</span>
                @endif
            </div>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $post->title }}</h1>
        <div class="text-gray-700 leading-relaxed whitespace-pre-wrap mb-4">{{ $post->body }}</div>

        @php
            $mediaItems = [];
            if (!empty($post->media)) {
                foreach ($post->media as $path) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $type = in_array($ext, ['mp4', 'mov', 'webm']) ? 'video' : 'image';
                    $mediaItems[] = ['url' => Storage::url($path), 'type' => $type];
                }
            }
            $visibleItems = array_slice($mediaItems, 0, 4);
            $totalCount = count($mediaItems);
        @endphp

        @if(!empty($post->media))
            <div class="grid grid-cols-2 gap-3 mt-6 mb-4">
                @foreach($visibleItems as $index => $item)
                    <div class="relative rounded-xl overflow-hidden bg-gray-100 border border-gray-100 aspect-square flex items-center justify-center cursor-pointer" @click="openSlideshow({{ json_encode($mediaItems) }}, {{ $index }})">
                        @if($item['type'] === 'video')
                            <div class="relative w-full h-full bg-black flex items-center justify-center">
                                <video src="{{ $item['url'] }}" class="w-full h-full object-cover pointer-events-none" muted preload="metadata"></video>
                                <div class="absolute inset-0 flex items-center justify-center bg-black/30 pointer-events-none">
                                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-3 border border-white/40 shadow-lg">
                                        <svg class="w-8 h-8 text-white fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                        @else
                            <img src="{{ $item['url'] }}" class="w-full h-full object-cover" alt="Post media">
                        @endif

                        @if ($index === 3 && $totalCount > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-2xl">
                                +{{ $totalCount - 3 }} more
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Comments Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Comments ({{ $post->comments->count() }})</h3>
        <div class="space-y-6">
            @foreach($post->comments as $comment)
                <div class="flex space-x-3">
                    <div class="shrink-0">
                        @if ($comment->user && $comment->user->profile_photo_path)
                            <img src="{{ Storage::url($comment->user->profile_photo_path) }}" alt="{{ $comment->user->name }}" class="w-9 h-9 rounded-full object-cover">
                        @else
                            <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs">
                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 rounded-xl px-4 py-3 inline-block w-full">
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center">
                                    @if($comment->user)
                                        <a href="{{ route('admin.members.show', $comment->user) }}" wire:navigate class="text-sm font-semibold text-gray-900 hover:text-primary-600 transition">{{ $comment->user->name }}</a>
                                    @else
                                        <p class="text-sm font-semibold text-gray-900">Unknown</p>
                                    @endif
                                    <span class="text-xs text-gray-400 ml-3">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                {{-- Security: Hide comment delete button if moderator is viewing a staff member's post --}}
                                @if(!auth()->user()->isModerator() || !$post->user->isStaff())
                                    <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->body }}</p>
                        </div>

                        @if($comment->replies->isNotEmpty())
                            <div class="mt-3 ml-4 pl-4 border-l border-gray-100 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="flex space-x-2">
                                        <div class="shrink-0">
                                            @if ($reply->user && $reply->user->profile_photo_path)
                                                <img src="{{ Storage::url($reply->user->profile_photo_path) }}" alt="{{ $reply->user->name }}" class="w-7 h-7 rounded-full object-cover">
                                            @else
                                                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs">
                                                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="bg-gray-50 rounded-xl px-3 py-2 inline-block w-full">
                                            <div class="flex justify-between items-center mb-1">
                                                <div class="flex items-center">
                                                    @if($reply->user)
                                                        <a href="{{ route('admin.members.show', $reply->user) }}" wire:navigate class="text-xs font-semibold text-gray-900 hover:text-primary-600 transition">{{ $reply->user->name }}</a>
                                                    @else
                                                        <p class="text-xs font-semibold text-gray-900">Unknown</p>
                                                    @endif
                                                    <span class="text-xs text-gray-400 ml-3">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                {{-- Security: Hide reply delete button if moderator is viewing a staff member's post --}}
                                                @if(!auth()->user()->isModerator() || !$post->user->isStaff())
                                                    <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="Are you sure you want to delete this reply?" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-700 mt-1">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Slideshow Modal -->
    <div x-show="showSlideshow" x-cloak class="fixed inset-0 z-[60] bg-black/90 flex items-center justify-center" x-transition.opacity @click.self="closeSlideshow()">

        <!-- Close Button -->
        <button @click="closeSlideshow()" class="absolute top-6 right-6 text-white p-2 rounded-full bg-white/10 hover:bg-white/20 transition z-20">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <!-- Previous Button -->
        <button @click="prevSlide()" x-show="slides.length > 1" class="absolute left-4 sm:left-6 text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition z-20">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </button>

        <!-- Slide Content -->
        <div class="w-full max-w-5xl h-full flex items-center justify-center p-4 sm:p-10">
            <template x-for="(slide, i) in slides" :key="i">
                <div x-show="currentSlide === i" class="flex items-center justify-center w-full h-full" x-transition>

                    <!-- Custom Video Player Block -->
                    <template x-if="slide.type === 'video'">
                        <div x-ref="videoContainer" class="relative inline-flex max-h-[85vh] max-w-full items-center justify-center" x-data="{
                            isPlaying: false,
                            isMuted: false,
                            isFullscreen: false,
                            progress: 0,
                            duration: 0,
                            currentTime: 0,
                            init() {
                                const video = this.$refs.videoPlayer;
                                video.addEventListener('loadedmetadata', () => {
                                    this.duration = video.duration;
                                });
                                video.addEventListener('timeupdate', () => {
                                    if (video.duration) {
                                        this.progress = (video.currentTime / video.duration) * 100;
                                        this.currentTime = video.currentTime;
                                    }
                                });
                                video.addEventListener('play', () => this.isPlaying = true);
                                video.addEventListener('pause', () => this.isPlaying = false);
                                video.addEventListener('ended', () => this.isPlaying = false);

                                document.addEventListener('fullscreenchange', () => {
                                    this.isFullscreen = !!document.fullscreenElement;
                                });

                                video.pause();
                            },
                            togglePlay() {
                                const video = this.$refs.videoPlayer;
                                if (video.paused) video.play(); else video.pause();
                            },
                            toggleMute() {
                                const video = this.$refs.videoPlayer;
                                video.muted = !video.muted;
                                this.isMuted = video.muted;
                            },
                            toggleFullscreen() {
                                const container = this.$refs.videoContainer;
                                if (!document.fullscreenElement) {
                                    if (container.requestFullscreen) container.requestFullscreen();
                                } else {
                                    if (document.exitFullscreen) document.exitFullscreen();
                                }
                            },
                            seek(e) {
                                const video = this.$refs.videoPlayer;
                                const rect = e.currentTarget.getBoundingClientRect();
                                const pos = (e.clientX - rect.left) / rect.width;
                                video.currentTime = pos * video.duration;
                            },
                            formatTime(s) {
                                if (isNaN(s) || s === Infinity) return '0:00';
                                const m = Math.floor(s / 60);
                                const sec = Math.floor(s % 60);
                                return m + ':' + (sec < 10 ? '0' : '') + sec;
                            }
                        }" x-init="init()">

                            <video x-ref="videoPlayer" :src="slide.url" class="slideshow-video max-h-[85vh] max-w-full object-contain rounded-lg shadow-2xl" @click="togglePlay()"></video>

                            <button @click="togglePlay()" class="absolute inset-0 flex items-center justify-center w-full h-full group">
                                <div x-show="!isPlaying" class="bg-black/50 backdrop-blur-sm rounded-full p-5 border border-white/30 transition group-hover:scale-110">
                                    <svg class="w-12 h-12 text-white fill-white ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                <div x-show="isPlaying" x-cloak class="bg-black/30 backdrop-blur-sm rounded-full p-5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-12 h-12 text-white fill-white" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                </div>
                            </button>

                            <div class="absolute bottom-0 left-0 right-0 flex items-center gap-2 px-2 pb-1 pt-4 text-white bg-gradient-to-t from-black/80 via-black/40 to-transparent rounded-b-lg" @click.stop>

                                <button @click="togglePlay()" class="hover:text-primary-400 transition shrink-0 p-1">
                                    <svg x-show="!isPlaying" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    <svg x-show="isPlaying" x-cloak class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                </button>

                                <span class="text-xs tabular-nums w-24 text-center shrink-0" x-text="formatTime(currentTime) + ' / ' + formatTime(duration)"></span>

                                <div class="flex-1 h-1 bg-white/30 rounded-full cursor-pointer relative group" @click="seek($event)">
                                    <div class="absolute top-0 left-0 h-full bg-primary-500 rounded-full group-hover:bg-primary-400 transition" :style="`width: ${progress}%`"></div>
                                    <div class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full shadow opacity-0 group-hover:opacity-100 transition" :style="`left: calc(${progress}% - 6px)`"></div>
                                </div>

                                <button @click="toggleMute()" class="hover:text-primary-400 transition shrink-0 p-1">
                                    <svg x-show="!isMuted" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                                    <svg x-show="isMuted" x-cloak class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.17v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
                                </button>

                                <button @click="toggleFullscreen()" class="hover:text-primary-400 transition shrink-0 p-1">
                                    <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l4 4m8-4h4m0 0v4m0-4l-4 4M4 16v4m0 0h4m-4 0l4-4m8 4l4-0m0 0v-4m0 4l-4-4" /></svg>
                                    <svg x-show="isFullscreen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" /></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="slide.type === 'image'">
                        <img :src="slide.url" class="max-h-[85vh] max-w-full object-contain rounded-lg shadow-2xl">
                    </template>
                </div>
            </template>
        </div>

        <button @click="nextSlide()" x-show="slides.length > 1" class="absolute right-4 sm:right-6 text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition z-20">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>

        <div x-show="slides.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white bg-black/40 px-4 py-1 rounded-full text-sm z-20">
            <span x-text="currentSlide + 1"></span> / <span x-text="slides.length"></span>
        </div>
    </div>
</div>