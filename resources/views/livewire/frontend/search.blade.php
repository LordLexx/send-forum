<?php

use App\Notifications\NewHug;
use App\Notifications\NewComment;
use App\Models\Post;
use App\Models\Category;
use App\Models\Event;
use App\Models\Hug;
use App\Models\Comment;
use App\Models\SavedPost;
use App\Models\PostReport;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Layout('layouts.forum-layout')] class extends Component {
    use WithPagination, WithFileUploads;

    public $categories; // All active categories (for modals)
    public $feedCategories; // Only priority categories (for sidebar)
    public $nextEvent;
    public $search = '';
    public $searchFilter = 'all';

    public $badgeClasses = [
        'bg-primary-50 text-primary-700',
        'bg-orange-50 text-orange-700',
        'bg-yellow-50 text-yellow-700',
        'bg-blue-50 text-blue-700',
        'bg-green-50 text-green-700',
        'bg-purple-50 text-purple-700',
    ];

    public $activeReplyForm = null;
    public $replyText = '';

    public $commentStates = [];
    public $expandedReplies = [];

    // Edit Post Properties
    public $editPostId = null;
    public $editTitle = '';
    public $editBody = '';
    public $editCategoryId = '';
    public $editMedia = [];
    public $editUploads = [];

    // Upload error messages
    public $uploadError = null;
    public $editUploadError = null;

    // Edit Comment Properties
    public $editCommentId = null;
    public $editCommentBody = '';

    // Allowed extensions
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'webm'];

    public function mount(): void
    {
        $this->search = request()->query('q', '');

        // All active categories for the dropdown in modals
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

    public function updatingSearchFilter()
    {
        $this->resetPage();
    }

    public function highlight($text)
    {
        if (!$this->search) return e($text);
        $escapedSearch = e($this->search);
        return str_ireplace($escapedSearch, '<span class="search-highlight">' . $escapedSearch . '</span>', e($text));
    }

    public function toggleComments($postId, $totalComments = 0): void
    {
        $currentState = $this->commentStates[$postId] ?? 'default';
        if ($currentState === 'default') {
            $this->commentStates[$postId] = $totalComments > 1 ? 'expanded' : 'hidden';
        } elseif ($currentState === 'expanded') {
            $this->commentStates[$postId] = 'hidden';
        } else {
            $this->commentStates[$postId] = 'default';
        }
    }

    public function toggleReplies($commentId): void
    {
        if (in_array($commentId, $this->expandedReplies)) {
            $this->expandedReplies = array_diff($this->expandedReplies, [$commentId]);
        } else {
            $this->expandedReplies[] = $commentId;
        }
    }

    public function removeUpload($index): void
    {
        $index = (int) $index;
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function removeEditUpload($index): void
    {
        $index = (int) $index;
        if (isset($this->editUploads[$index])) {
            unset($this->editUploads[$index]);
            $this->editUploads = array_values($this->editUploads);
        }
    }

    public function updatedUploads(): void
    {
        $this->uploadError = null;
        $removedAny = false;

        foreach ($this->uploads as $key => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType();

            $isAllowedExt = in_array($extension, $this->allowedExtensions);
            $isImage = str_starts_with($mime, 'image/');
            $isVideo = str_starts_with($mime, 'video/');

            if (!$isAllowedExt || (!$isImage && !$isVideo)) {
                $fileName = $file->getClientOriginalName();
                unset($this->uploads[$key]);
                $this->uploadError = "Error! \"{$fileName}\" isn't a supported file type. Only images (JPG, PNG, WebP) and videos (MP4, MOV, WebM) are allowed.";
                $this->dispatch('notify', type: 'error', message: $this->uploadError);
                $removedAny = true;
            }
        }

        if ($removedAny) {
            $this->uploads = array_values($this->uploads);
        }
    }

    public function updatedEditUploads(): void
    {
        $this->editUploadError = null;
        $removedAny = false;

        foreach ($this->editUploads as $key => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType();

            $isAllowedExt = in_array($extension, $this->allowedExtensions);
            $isImage = str_starts_with($mime, 'image/');
            $isVideo = str_starts_with($mime, 'video/');

            if (!$isAllowedExt || (!$isImage && !$isVideo)) {
                $fileName = $file->getClientOriginalName();
                unset($this->editUploads[$key]);
                $this->editUploadError = "Error! \"{$fileName}\" isn't a supported file type. Only images (JPG, PNG, WebP) and videos (MP4, MOV, WebM) are allowed.";
                $this->dispatch('notify', type: 'error', message: $this->editUploadError);
                $removedAny = true;
            }
        }

        if ($removedAny) {
            $this->editUploads = array_values($this->editUploads);
        }
    }

    public function toggleHug($postId): void
    {
        $post = Post::find($postId);
        if (!$post) return;

        $existingHug = Hug::where('post_id', $postId)->where('user_id', Auth::id())->first();

        if ($existingHug) {
            $existingHug->delete();
            $post->decrement('hugs_count');
        } else {
            Hug::create(['post_id' => $postId, 'user_id' => Auth::id()]);
            $post->increment('hugs_count');

            if ($post->user_id != Auth::id()) {
                $post->user->notify(new NewHug($post, Auth::user()));
            }
        }
    }

    public function toggleSavePost($postId): void
    {
        $existing = SavedPost::where('user_id', Auth::id()).where('post_id', $postId).first();
        if ($existing) {
            $existing->delete();
        } else {
            SavedPost::create(['user_id' => Auth::id(), 'post_id' => $postId]);
        }
    }

    public function toggleReport($postId): void
    {
        $post = Post::find($postId);
        if (!$post) return;

        $existing = PostReport::where('post_id', $postId).where('user_id', Auth::id()).first();

        if ($existing) {
            $existing->delete();
            $this->dispatch('notify', type: 'success', message: 'Report retracted successfully.');
        } else {
            PostReport::create(['post_id' => $postId, 'user_id' => Auth::id()]);
            $this->dispatch('notify', type: 'success', message: 'Post reported. Admins will review it shortly.');
        }
    }

    public function openReplyForm($formId, $replyToName = null): void
    {
        $this->cancelEditComment();
        $this->activeReplyForm = $formId;
        $this->replyText = $replyToName ? '@' . preg_replace('/\s+/', '', $replyToName) . ' ' : '';
    }

    public function closeReplyForm(): void
    {
        $this->activeReplyForm = null;
        $this->replyText = '';
    }

    public function openEditCommentForm($commentId): void
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == Auth::id()) {
            $this->editCommentId = $commentId;
            $this->editCommentBody = $comment->body;
            $this->closeReplyForm();
        }
    }

    public function cancelEditComment(): void
    {
        $this->reset(['editCommentId', 'editCommentBody']);
    }

    public function updateComment($commentId): void
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == Auth::id()) {
            $this->validate([
                'editCommentBody' => 'required|string|max:1000',
            ]);

            $comment->update(['body' => $this->editCommentBody]);
            $this->cancelEditComment();
            $this->dispatch('notify', type: 'success', message: 'Comment updated successfully!');
        }
    }

    public function deleteComment($commentId): void
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id == Auth::id()) {
            $post = $comment->post;
            $decrementAmount = 1 + $comment->replies->count();

            $comment->delete();

            if ($post) {
                $post->decrement('replies_count', $decrementAmount);
            }

            $this->dispatch('notify', type: 'success', message: 'Comment deleted successfully!');
        }
    }

    public function postComment($postId, $parentId = null): void
    {
        $this->validate([
            'replyText' => 'required|string|max:1000',
        ]);

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'body' => $this->replyText,
        ]);

        $post = Post::find($postId);
        $post->increment('replies_count');

        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if ($parentComment && $parentComment->user_id != Auth::id()) {
                $parentComment->user->notify(new NewComment($post, Auth::user()));
            }
        } else {
            if ($post->user_id != Auth::id()) {
                $post->user->notify(new NewComment($post, Auth::user()));
            }
        }

        $this->reset(['replyText', 'activeReplyForm']);
    }

    public function openEditModal($postId): void
    {
        $post = Post::find($postId);
        if ($post && $post->user_id == Auth::id()) {
            $this->editPostId = $post->id;
            $this->editTitle = $post->title;
            $this->editBody = $post->body;
            $this->editCategoryId = $post->category_id;
            $this->editMedia = $post->media ?? [];
            $this->editUploads = [];
            $this->editUploadError = null;
            $this->dispatch('open-edit-modal');
        }
    }

    public function removeExistingMedia($index): void
    {
        $index = (int) $index;
        if (isset($this->editMedia[$index])) {
            unset($this->editMedia[$index]);
            $this->editMedia = array_values($this->editMedia);
        }
    }

    public function updatePost(): void
    {
        $post = Post::find($this->editPostId);
        if ($post && $post->user_id == Auth::id()) {
            $this->validate([
                'editTitle' => 'required|string|max:255',
                'editBody' => 'required|string',
                'editCategoryId' => 'required|exists:categories,id',
                'editUploads' => 'nullable|array|max:10',
                'editUploads.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:51200',
            ]);

            $originalMedia = $post->media ?? [];
            $keptMedia = $this->editMedia;

            foreach ($originalMedia as $oldPath) {
                if (!in_array($oldPath, $keptMedia)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $newMediaPaths = [];
            if (!empty($this->editUploads)) {
                foreach ($this->editUploads as $file) {
                    $newMediaPaths[] = $file->store('post-media', 'public');
                }
            }

            $finalMedia = array_merge($keptMedia, $newMediaPaths);

            $post->update([
                'title' => $this->editTitle,
                'body' => $this->editBody,
                'category_id' => $this->editCategoryId,
                'media' => $finalMedia,
            ]);

            $this->reset(['editPostId', 'editTitle', 'editBody', 'editCategoryId', 'editMedia', 'editUploads', 'editUploadError']);
            Session::flash('success', 'Post updated successfully!');

            $newCategory = Category::find($post->category_id);
            $this->redirect(route('category', $newCategory));
        }
    }

    public function deletePost($postId): void
    {
        $post = Post::find($postId);
        if ($post && $post->user_id == Auth::id()) {
            if (!empty($post->media)) {
                foreach ($post->media as $mediaPath) {
                    Storage::disk('public')->delete($mediaPath);
                }
            }
            $post->delete();
            Session::flash('success', 'Post deleted successfully!');
            $this->redirect(route('feed'));
        }
    }

    public function render(): mixed
    {
        $searchTerm = '%' . $this->search . '%';

        $posts = collect();
        $events = collect();
        $documents = collect();
        $links = collect();

        if ($this->searchFilter === 'all' || $this->searchFilter === 'posts') {
            $posts = Post::where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('body', 'like', $searchTerm);
            })
            ->where(function ($query) {
                // Show published posts to everyone
                $query->where('status', 'published')
                      // Show hidden posts only to the user who created them
                      ->orWhere('user_id', auth()->id());

                // Allow admins to see hidden posts on the frontend as well
                if (auth()->user()->is_admin) {
                    $query->orWhere('status', 'hidden');
                }
            })
            ->with([
                'user', 'category',
                'hugs' => fn($q) => $q->where('user_id', auth()->id()),
                'saves' => fn($q) => $q->where('user_id', auth()->id()),
                'reports' => fn($q) => $q->where('user_id', auth()->id()),
                'comments' => fn($q) => $q->orderBy('created_at', 'asc')->with('user'),
                'comments.replies' => fn($q) => $q->orderBy('created_at', 'asc')->with('user')
            ])
            ->latest()
            ->paginate(10);
        }

        if ($this->searchFilter === 'all' || $this->searchFilter === 'events') {
            $events = Event::where('title', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm)
                ->latest()
                ->get();
        }

        if ($this->searchFilter === 'all' || $this->searchFilter === 'resources') {
            $documents = Resource::where('status', 'published')
                ->where('type', 'file')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                })
                ->latest()
                ->get();

            $links = Resource::where('status', 'published')
                ->where('type', 'link')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                })
                ->latest()
                ->get();
        }

        return view('livewire.frontend.search', [
            'posts' => $posts,
            'events' => $events,
            'documents' => $documents,
            'links' => $links,
            'categories' => $this->categories,
            'feedCategories' => $this->feedCategories,
            'nextEvent' => $this->nextEvent,
            'badgeClasses' => $this->badgeClasses
        ]);
    }
}; ?>

<div x-data="{
        showEditPostModal: false,
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
    }" @open-edit-modal.window="showEditPostModal = true">

    @section('title', 'Search Results · Slough SEND PCF')

    <!-- ===== MAIN LAYOUT ===== -->
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Search Header Banner -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-4 text-center md:text-left">
                    <div class="w-14 h-14 rounded-full bg-primary-50 flex items-center justify-center text-2xl border border-primary-100 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Search Results</h1>
                        <p class="text-gray-500 mt-1 text-sm">Showing results for "<span class="font-semibold text-primary-700">{{ $this->search }}</span>"</p>
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

                <!-- ===== LEFT SIDEBAR ===== -->
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
                                <a href="{{ route('resources') }}" class="flex items-center px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
                                    <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Resource Hub
                                </a>
                            </nav>
                        </div>
                    </div>
                </aside>

                <!-- ===== MAIN FEED ===== -->
                <main class="lg:col-span-6">
                    <!-- Filter Controls -->
                    <div class="flex items-center justify-between mb-6">
                        <div id="filter-container" class="flex space-x-1 bg-white p-1 rounded-full border border-gray-100 shadow-sm relative">
                            <div id="filter-slider" wire:ignore class="absolute top-1 bottom-1 rounded-full bg-gray-900 shadow-sm transition-all duration-300 ease-out"></div>

                            <button wire:click="set('searchFilter', 'all')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $searchFilter === 'all' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                            <button wire:click="set('searchFilter', 'posts')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $searchFilter === 'posts' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Posts</button>
                            <button wire:click="set('searchFilter', 'resources')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $searchFilter === 'resources' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Resources</button>
                            <button wire:click="set('searchFilter', 'events')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $searchFilter === 'events' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Events</button>
                        </div>
                    </div>

                    <!-- Search Results List -->
                    <div class="space-y-6">

                        @if($searchFilter === 'all' || $searchFilter === 'events')
                            @foreach($events as $event)
                                <a href="#" class="post-card block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">Event</p>
                                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y · h:i A') }}</p>
                                        </div>
                                    </div>
                                    <h2 class="post-title text-xl font-bold text-gray-900 mb-2 transition">{!! $this->highlight($event->title) !!}</h2>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{!! $this->highlight($event->description) !!}</p>
                                </a>
                            @endforeach
                        @endif

                        @if($searchFilter === 'all' || $searchFilter === 'resources')
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

                                <div class="resource-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                    <div class="flex items-center mb-4 p-6 pb-0">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">Document Resource</p>
                                            <p class="text-xs text-gray-400">{{ $badgeText }} · {{ $doc->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="px-6 pb-6">
                                        <div class="card-thumbnail relative h-40 {{ $bgColor }} flex items-center justify-center overflow-hidden rounded-xl mb-4">
                                            @if($isImage)
                                                <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover transition duration-300">
                                            @else
                                                <svg class="h-16 w-16 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                            @endif
                                            <span class="absolute top-3 right-3 {{ $badgeClass }} text-xs px-2 py-1 rounded-full font-bold shadow-sm">{{ $badgeText }}</span>
                                        </div>
                                        <h2 class="resource-title font-bold text-gray-900 text-base mb-1 transition">{!! $this->highlight($doc->title) !!}</h2>
                                        <p class="text-gray-500 text-xs mb-4 line-clamp-3">{!! $this->highlight($doc->description) !!}</p>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="w-full bg-orange-500 text-white text-center text-sm font-medium py-2 rounded-lg hover:bg-orange-600 transition flex items-center justify-center">
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

                                <div class="resource-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition duration-300">
                                    <div class="flex items-center mb-4 p-6 pb-0">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">Link Resource</p>
                                            <p class="text-xs text-gray-400">{{ $badgeText }} · {{ $link->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="px-6 pb-6">
                                        <div class="card-thumbnail relative h-40 bg-gray-900 overflow-hidden rounded-xl mb-4">
                                            @if($thumbnailUrl)
                                                <img src="{{ $thumbnailUrl }}" alt="{{ $link->title }}" class="w-full h-full object-cover transition duration-300 {{ $isVideo ? 'opacity-80' : 'opacity-100' }}" onerror="this.style.display='none'; document.getElementById('search-fallback-{{ $link->id }}').classList.remove('hidden');">
                                                <div id="search-fallback-{{ $link->id }}" class="hidden absolute inset-0 bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                                    <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                                </div>
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                                    <svg class="h-16 w-16 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                                </div>
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
                                        <h2 class="resource-title font-bold text-gray-900 text-base mb-1 transition">{!! $this->highlight($link->title) !!}</h2>
                                        <p class="text-gray-500 text-xs mb-4 line-clamp-3">{!! $this->highlight($link->description) !!}</p>
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

                            @if($documents->isEmpty() && $links->isEmpty())
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center text-gray-500">
                                    <p>No resources found matching your search.</p>
                                </div>
                            @endif
                        @endif

                        @if($searchFilter === 'all' || $searchFilter === 'posts')
                            @forelse($posts as $post)
                                @php
                                    $badgeClass = $badgeClasses[$post->category->id % count($badgeClasses)];
                                    $hasHugged = $post->hugs->isNotEmpty();
                                    $hasSaved = $post->saves->isNotEmpty();
                                    $hasReported = $post->reports->isNotEmpty();
                                    $postTime = $post->created_at->diffInSeconds() < 60 ? 'Just now' : $post->created_at->diffForHumans();

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

                                    $commentState = $this->commentStates[$post->id] ?? 'default';
                                    if ($commentState === 'default') {
                                        $commentsToShow = $post->comments->take(1);
                                    } elseif ($commentState === 'expanded') {
                                        $commentsToShow = $post->comments;
                                    } else {
                                        $commentsToShow = [];
                                    }

                                    $isLongPost = strlen($post->body) > 200;
                                @endphp

                                <div class="post-card block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                                    <div class="flex items-center mb-4">
                                        @if ($post->user->profile_photo_path)
                                            <img src="{{ Storage::url($post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold mr-3">
                                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $post->user->name }}</p>
                                            <p class="text-xs text-gray-400">posted {{ $postTime }}</p>
                                        </div>
                                        <span class="ml-auto text-xs {{ $badgeClass }} px-3 py-1 rounded-full font-medium shrink-0">{{ $post->category->icon }} {{ $post->category->name }}</span>
                                    </div>

                                    <h2 class="post-title text-xl font-bold text-gray-900 mb-2 transition">{!! $this->highlight($post->title) !!}</h2>

                                    <div x-data="{ expanded: false }" class="text-gray-600 text-sm mb-4">
                                        <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{!! $this->highlight($post->body) !!}</div>
                                        @if($isLongPost)
                                            <button @click="expanded = !expanded" class="text-primary-600 font-medium hover:text-primary-500 transition cursor-pointer focus:outline-none text-xs mt-1">
                                                <span x-show="!expanded">See more</span>
                                                <span x-show="expanded" x-cloak>See less</span>
                                            </button>
                                        @endif
                                    </div>

                                    @if(!empty($post->media))
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            @foreach($visibleItems as $index => $item)
                                                <div class="relative rounded-xl overflow-hidden bg-gray-101 border border-gray-100 aspect-square flex items-center justify-center cursor-pointer" @click="openSlideshow({{ json_encode($mediaItems) }}, {{ $index }})">
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

                                    <!-- Action Bar (Mobile Optimized - Icons Only on Small Screens) -->
                                    <div class="flex items-center text-sm text-gray-400 pt-4 border-t border-gray-50 gap-x-3 sm:gap-x-6">
                                        <button wire:click="toggleHug({{ $post->id }})" class="flex items-center hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                            <svg class="h-5 w-5 sm:mr-1.5 {{ $hasHugged ? 'text-orange-500 fill-orange-500' : 'text-gray-400 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                            <span class="{{ $hasHugged ? 'text-orange-500 font-medium' : '' }}">{{ $post->hugs_count }} <span class="hidden sm:inline"></span></span>
                                        </button>

                                        <button wire:click="openReplyForm('post-{{ $post->id }}')" class="flex items-center hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                            <svg class="h-5 w-5 sm:mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582, 9 8z" /></svg>
                                            <span>{{ $post->replies_count }} <span class="hidden sm:inline"></span></span>
                                        </button>

                                        <button wire:click="toggleSavePost({{ $post->id }})" class="flex items-center hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                            <svg class="h-5 w-5 sm:mr-1.5 {{ $hasSaved ? 'text-primary-500 fill-primary-500' : 'text-gray-400 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                            <span class="hidden sm:inline {{ $hasSaved ? 'text-primary-500 font-medium' : '' }}"></span>
                                        </button>

                                        @if($post->user_id == auth()->id())
                                            <button wire:click="openEditModal({{ $post->id }})" class="flex items-center ml-auto sm:ml-0 hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                                <svg class="h-5 w-5 sm:mr-1.5 text-gray-400 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                <span class="hidden sm:inline"></span>
                                            </button>

                                            <button wire:click="deletePost({{ $post->id }})" wire:confirm="Are you sure you want to delete this post? This cannot be undone." class="flex items-center hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                                <svg class="h-5 w-5 sm:mr-1.5 text-gray-400 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span class="hidden sm:inline"></span>
                                            </button>
                                        @endif

                                        <button wire:click="toggleReport({{ $post->id }})" class="flex items-center hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                            <svg class="h-5 w-5 sm:mr-1.5 {{ $hasReported ? 'text-red-500 fill-red-500' : 'text-gray-400 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" /></svg>
                                            <span class="hidden sm:inline {{ $hasReported ? 'text-red-500 font-medium' : '' }}">{{ $hasReported ? 'Reported' : '' }}</span>
                                        </button>
                                    </div>

                                    <!-- ===== COMMENTS SECTION ===== -->
                                    <div class="mt-4 space-y-4">

                                        @if($activeReplyForm === 'post-' . $post->id)
                                            <form wire:submit="postComment({{ $post->id }})" class="flex flex-col space-y-2">
                                                <textarea wire:model="replyText" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Write a reply..."></textarea>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" wire:click="closeReplyForm" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                    <button type="submit" class="px-4 py-1 text-xs text-white bg-orange-500 rounded-lg hover:bg-orange-600">Post Reply</button>
                                                </div>
                                            </form>
                                        @endif

                                        @foreach($commentsToShow as $comment)
                                            @php
                                                $commentTime = $comment->created_at->diffInSeconds() < 60 ? 'Just now' : $comment->created_at->diffForHumans();
                                                $isLongComment = strlen($comment->body) > 200;
                                            @endphp
                                            <div class="flex space-x-3" wire:key="comment-{{ $comment->id }}">
                                                <div class="shrink-0">
                                                    @if ($comment->user->profile_photo_path)
                                                        <img src="{{ Storage::url($comment->user->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="bg-gray-50 rounded-xl px-3 py-2 inline-block">
                                                        <p class="text-xs font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                        @if($editCommentId === $comment->id)
                                                            <form wire:submit="updateComment({{ $comment->id }})" class="mt-1 flex flex-col space-y-2">
                                                                <textarea wire:model="editCommentBody" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                                                                <div class="flex justify-end space-x-2">
                                                                    <button type="button" wire:click="cancelEditComment" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                                    <button type="submit" class="px-4 py-1 text-xs text-white bg-orange-500 rounded-lg hover:bg-orange-600">Save</button>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <div x-data="{ expanded: false }" class="text-sm text-gray-700 mt-1">
                                                                <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{{ $comment->body }}</div>
                                                                @if($isLongComment)
                                                                    <button @click="expanded = !expanded" class="text-primary-600 font-medium hover:text-primary-500 transition cursor-pointer focus:outline-none text-xs mt-1">
                                                                        <span x-show="!expanded">See more</span>
                                                                        <span x-show="expanded" x-cloak>See less</span>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center mt-1 ml-1">
                                                        <p class="text-xs text-gray-400 mr-3">{{ $commentTime }}</p>
                                                        <button wire:click="openReplyForm('comment-{{ $comment->id }}', {{ json_encode($comment->user->name) }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium">Reply</button>

                                                        @if($comment->user_id == auth()->id())
                                                            <button wire:click="openEditCommentForm({{ $comment->id }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium ml-3">Edit</button>
                                                            <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="text-xs text-gray-500 hover:text-red-500 font-medium ml-3">Delete</button>
                                                        @endif
                                                    </div>

                                                    @php $isReplyExpanded = in_array($comment->id, $this->expandedReplies); @endphp

                                                    @if($comment->replies->isNotEmpty())
                                                        <button wire:click="toggleReplies({{ $comment->id }})" class="flex items-center text-xs text-primary-600 hover:text-primary-500 font-medium ml-1 mt-2">
                                                            <svg class="w-3 h-3 mr-1 transition-transform transform {{ $isReplyExpanded ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                            @if($isReplyExpanded)
                                                                Hide replies
                                                            @else
                                                                View {{ $comment->replies->count() }} replies
                                                            @endif
                                                        </button>
                                                    @endif

                                                    @if($isReplyExpanded || $activeReplyForm === 'comment-' . $comment->id)
                                                        <div class="ml-6 sm:ml-10 pl-4 border-l border-gray-100 space-y-3 mt-3" wire:key="replies-{{ $comment->id }}">
                                                            @foreach($comment->replies as $reply)
                                                                @php
                                                                    $replyTime = $reply->created_at->diffInSeconds() < 60 ? 'Just now' : $reply->created_at->diffForHumans();
                                                                    $isLongReply = strlen($reply->body) > 200;
                                                                @endphp
                                                                <div class="flex space-x-3" wire:key="reply-{{ $reply->id }}">
                                                                    <div class="shrink-0">
                                                                        @if ($reply->user->profile_photo_path)
                                                                            <img src="{{ Storage::url($reply->user->profile_photo_path) }}" class="w-7 h-7 rounded-full object-cover">
                                                                        @else
                                                                            <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="bg-gray-50 rounded-xl px-3 py-2 inline-block">
                                                                            <p class="text-xs font-semibold text-gray-900">{{ $reply->user->name }}</p>
                                                                            @if($editCommentId === $reply->id)
                                                                                <form wire:submit="updateComment({{ $reply->id }})" class="mt-1 flex flex-col space-y-2">
                                                                                    <textarea wire:model="editCommentBody" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                                                                                    <div class="flex justify-end space-x-2">
                                                                                        <button type="button" wire:click="cancelEditComment" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                                                        <button type="submit" class="px-4 py-1 text-xs text-white bg-orange-500 rounded-lg hover:bg-orange-600">Save</button>
                                                                                    </div>
                                                                                </form>
                                                                            @else
                                                                                <div x-data="{ expanded: false }" class="text-sm text-gray-700 mt-1">
                                                                                    <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{{ $reply->body }}</div>
                                                                                    @if($isLongReply)
                                                                                        <button @click="expanded = !expanded" class="text-primary-600 font-medium hover:text-primary-500 transition cursor-pointer focus:outline-none text-xs mt-1">
                                                                                            <span x-show="!expanded">See more</span>
                                                                                            <span x-show="expanded" x-cloak>See less</span>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex items-center mt-1 ml-1">
                                                                            <p class="text-xs text-gray-400 mr-3">{{ $replyTime }}</p>
                                                                            <button wire:click="openReplyForm('comment-{{ $comment->id }}', {{ json_encode($reply->user->name) }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium">Reply</button>

                                                                            @if($reply->user_id == auth()->id())
                                                                                <button wire:click="openEditCommentForm({{ $reply->id }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium ml-3">Edit</button>
                                                                                <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="Are you sure you want to delete this reply?" class="text-xs text-gray-500 hover:text-red-500 font-medium ml-3">Delete</button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            @if($activeReplyForm === 'comment-' . $comment->id)
                                                                <form wire:submit="postComment({{ $post->id }}, {{ $comment->id }})" class="flex flex-col space-y-2 mt-2">
                                                                    <textarea wire:model="replyText" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Write a reply..."></textarea>
                                                                    <div class="flex justify-end space-x-2">
                                                                        <button type="button" wire:click="closeReplyForm" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                                        <button type="submit" class="px-4 py-1 text-xs text-white bg-orange-500 rounded-lg hover:bg-orange-600">Post Reply</button>
                                                                    </div>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        @if($post->comments->count() > 0)
                                            <button wire:click="toggleComments({{ $post->id }}, {{ $post->comments->count() }})" class="text-xs text-primary-600 hover:text-primary-500 font-medium ml-1 mt-2">
                                                @if($commentState === 'default')
                                                    @if($post->comments->count() > 1)
                                                        View {{ $post->comments->count() - 1 }} more comments...
                                                    @else
                                                        Hide comments
                                                    @endif
                                                @elseif($commentState === 'expanded')
                                                    Hide comments
                                                @else
                                                    See comments
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                @if($searchFilter === 'all' || $searchFilter === 'posts')
                                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center text-gray-500">
                                        <p>No posts found matching your search.</p>
                                    </div>
                                @endif
                            @endforelse
                        @endif

                    </div>

                    @if($searchFilter === 'all' || $searchFilter === 'posts')
                        <div class="text-center mt-8">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </main>

                <!-- ===== RIGHT SIDEBAR ===== -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="space-y-6 sticky top-24">
                        @if($nextEvent)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="bg-primary-50 p-4 border-b border-primary-100">
                                    <h3 class="font-bold text-primary-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Next Event
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

    <!-- ========================================== -->
    <!-- ===== MODAL: EDIT POST =================== -->
    <!-- ========================================== -->
    <div x-show="showEditPostModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-transition.opacity>
        <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showEditPostModal = false"></div>

            <div x-show="showEditPostModal" x-transition class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="updatePost" enctype="multipart/form-data">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Edit Your Post</h3>
                            <button type="button" @click="showEditPostModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select wire:model="editCategoryId" class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent rounded-lg sm:text-sm @error('editCategoryId') border-red-500 @enderror">
                                    <option value="">Select a Category...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('editCategoryId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" wire:model="editTitle" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('editTitle') border-red-500 @enderror">
                                @error('editTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Post</label>
                                <textarea wire:model="editBody" rows="4" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('editBody') border-red-500 @enderror"></textarea>
                                @error('editBody') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Attached Media</label>

                                @if(!empty($editMedia))
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        @foreach($editMedia as $index => $mediaPath)
                                            <div wire:key="edit-existing-{{ $index }}" class="relative group">
                                                <div class="rounded-lg overflow-hidden bg-gray-100 border border-gray-200 aspect-square flex items-center justify-center">
                                                    @php
                                                        $extension = pathinfo($mediaPath, PATHINFO_EXTENSION);
                                                        $isVideo = in_array($extension, ['mp4', 'mov', 'webm']);
                                                    @endphp
                                                    @if($isVideo)
                                                        <video src="{{ Storage::url($mediaPath) }}" class="w-full h-full object-cover"></video>
                                                    @else
                                                        <img src="{{ Storage::url($mediaPath) }}" class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <button type="button" wire:click="removeExistingMedia('{{ $index }}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md opacity-0 group-hover:opacity-100 transition focus:outline-none">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition hover:border-primary-500 hover:bg-primary-50/30 cursor-pointer"
                                    @click="$refs.editFileInput.click()"
                                    @dragover.prevent="$event.currentTarget.classList.add('border-primary-500', 'bg-primary-50/30')"
                                    @dragleave.prevent="$event.currentTarget.classList.remove('border-primary-500', 'bg-primary-50/30')"
                                    @drop.prevent="$event.currentTarget.classList.remove('border-primary-500', 'bg-primary-50/30'); $refs.editFileInput.files = $event.dataTransfer.files; $refs.editFileInput.dispatchEvent(new Event('change', { bubbles: true }))">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V16m0 0l4 4m20-4l-4 4m0 0l-4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="relative font-medium text-primary-600 hover:text-primary-500">Click to upload</span>
                                            <p class="pl-1">more files</p>
                                        </div>
                                        <p class="text-xs text-gray-500">Max 10 new files. PNG, JPG, MP4 up to 50MB</p>
                                    </div>
                                    <input type="file" x-ref="editFileInput" class="hidden" accept="image/*,video/*" multiple wire:model="editUploads">
                                </div>

                                <div wire:loading wire:target="editUploads" class="mt-3 text-center text-sm text-gray-500">
                                    Uploading files...
                                </div>

                                @if($editUploadError)
                                    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg flex items-start mt-3">
                                        <svg class="h-5 w-5 text-red-500 mr-2 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <p class="text-xs text-red-800">{{ $editUploadError }}</p>
                                    </div>
                                @endif

                                @if(!empty($editUploads))
                                    <div class="mt-3 grid grid-cols-3 gap-3">
                                        @foreach($editUploads as $file)
                                            <div wire:key="edit-upload-{{ $loop->index }}" class="relative group">
                                                <div class="rounded-lg overflow-hidden bg-gray-100 border border-gray-200 aspect-square flex items-center justify-center">
                                                    @php
                                                        $isImage = str_starts_with($file->getMimeType(), 'image/');
                                                        $isVideo = str_starts_with($file->getMimeType(), 'video/');
                                                    @endphp
                                                    @if ($isImage)
                                                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover">
                                                    @elseif ($isVideo)
                                                        <video src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover bg-black" controls preload="metadata"></video>
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-red-50">
                                                            <svg class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" wire:click="removeEditUpload('{{ $loop->index }}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md opacity-0 group-hover:opacity-100 transition focus:outline-none">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @error('editUploads.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                @error('editUploads') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-500 text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button" @click="showEditPostModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ===== MODAL: MEDIA SLIDESHOW ============= -->
    <!-- ========================================== -->
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

    <script>
        // Function to align the slider to the active button
        function alignFilterSlider() {
            const slider = document.getElementById('filter-slider');
            if (!slider) return;

            const activeBtn = document.querySelector('#filter-container button.text-white');
            if (activeBtn) {
                slider.style.left = activeBtn.offsetLeft + 'px';
                slider.style.width = activeBtn.offsetWidth + 'px';
            }
        }

        alignFilterSlider();
        window.addEventListener('resize', alignFilterSlider);
        document.addEventListener('livewire:navigated', alignFilterSlider);
        window.addEventListener('load', alignFilterSlider);

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

        if (!window._notyfListenerRegistered) {
            window._notyfListenerRegistered = true;
            window.addEventListener('notify', (e) => {
                const { type, message } = e.detail;
                const notyfInstance = window.notyf || (window.Laravel && window.Laravel.notyf) || null;
                if (notyfInstance) {
                    if (type === 'error') {
                        notyfInstance.error(message);
                    } else if (type === 'success') {
                        notyfInstance.success(message);
                    } else {
                        notyfInstance.open({ type: 'info', message: message });
                    }
                } else {
                    console.warn('[notify] Notyf instance not found. Message was:', message);
                }
            });
        }
    </script>

</div>