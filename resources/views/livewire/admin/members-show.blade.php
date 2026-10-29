<?php
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Hug;
use App\Models\Comment;
use App\Models\SavedPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination, WithFileUploads;

    public User $user;
    public bool $canManageUser = false;

    // Active tab: 'posts' | 'replies' | 'saved'
    public $activeTab = 'posts';

    // Edit Post Properties
    public $editPostId = null;
    public $editTitle = '';
    public $editBody = '';
    public $editCategoryId = '';
    public $editMedia = [];
    public $editUploads = [];
    public $uploads = [];

    // Reply / Comment state
    public $activeReplyForm = null;
    public $replyText = '';

    // Comment expansion state
    public $commentStates = [];
    public $expandedReplies = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        
        $loggedInUser = auth()->user();
        // Admin can manage everyone. Moderator can manage everyone EXCEPT admins.
        $this->canManageUser = $loggedInUser->isAdmin() || ($loggedInUser->isModerator() && !$user->isAdmin());
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
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
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function removeEditUpload($index): void
    {
        if (isset($this->editUploads[$index])) {
            unset($this->editUploads[$index]);
            $this->editUploads = array_values($this->editUploads);
        }
    }

    public function toggleHug($postId): void
    {
        $post = Post::find($postId);
        if (!$post) return;

        $existingHug = Hug::where('post_id', $postId)->where('user_id', auth()->id())->first();

        if ($existingHug) {
            $existingHug->delete();
            $post->decrement('hugs_count');
        } else {
            Hug::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
            ]);
            $post->increment('hugs_count');
        }
    }

    public function toggleSavePost($postId): void
    {
        $existing = SavedPost::where('user_id', Auth::id())->where('post_id', $postId)->first();

        if ($existing) {
            $existing->delete();
        } else {
            SavedPost::create(['user_id' => Auth::id(), 'post_id' => $postId]);
        }
    }

    public function openReplyForm($formId, $replyToName = null): void
    {
        $this->activeReplyForm = $formId;

        if ($replyToName) {
            $handle = '@' . preg_replace('/\s+/', '', $replyToName);
            $this->replyText = $handle . ' ';
        } else {
            $this->replyText = '';
        }
    }

    public function closeReplyForm(): void
    {
        $this->activeReplyForm = null;
        $this->replyText = '';
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
            $this->dispatch('open-edit-modal');
        }
    }

    public function removeExistingMedia($index): void
    {
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

            $this->reset(['editPostId', 'editTitle', 'editBody', 'editCategoryId', 'editMedia', 'editUploads']);
            $this->dispatch('notify', type: 'success', message: 'Post updated successfully!');
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
            $this->dispatch('notify', type: 'success', message: 'Post deleted successfully!');
        }
    }

    // --- ADMIN MODERATION METHODS ---
    public function hidePost($postId): void
    {
        if (!$this->canManageUser) return;
        $post = Post::find($postId);
        if ($post) {
            $post->update(['status' => 'hidden']);
            $this->dispatch('notify', type: 'success', message: 'Post hidden from public view.');
        }
    }

    public function unhidePost($postId): void
    {
        if (!$this->canManageUser) return;
        $post = Post::find($postId);
        if ($post) {
            $post->update(['status' => 'published']);
            $this->dispatch('notify', type: 'success', message: 'Post is now public.');
        }
    }

    public function deletePostAdmin($postId): void
    {
        if (!$this->canManageUser) return;
        $post = Post::find($postId);
        if ($post) {
            if (!empty($post->media)) {
                foreach ($post->media as $mediaPath) {
                    Storage::disk('public')->delete($mediaPath);
                }
            }
            $post->delete();
            $this->dispatch('notify', type: 'success', message: 'Post permanently deleted.');
        }
    }

    public function updateUserStatus($status): void
    {
        if (!$this->canManageUser) return;
        $this->user->update(['status' => $status]);
        $this->dispatch('notify', type: 'success', message: "User status updated to {$status}.");
    }

    public function updateUserRole($role): void
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('notify', type: 'error', message: 'Only admins can change user roles.');
            return;
        }
        
        $this->user->update(['role' => $role]);
        $this->dispatch('notify', type: 'success', message: "User role updated to {$role}.");
    }

    public function render(): mixed
    {
        $user = $this->user;

        $postsCount = Post::where('user_id', $user->id)->count();
        $hugsReceived = Post::where('user_id', $user->id)->sum('hugs_count');
        $repliesReceived = Comment::whereHas('post', fn($q) => $q->where('user_id', $user->id))->count();
        $savedCount = SavedPost::where('user_id', $user->id)->count();
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        $query = Post::with([
            'user', 'category',
            'hugs' => fn($q) => $q->where('user_id', auth()->id()),
            'saves' => fn($q) => $q->where('user_id', auth()->id()),
            'comments' => fn($q) => $q->orderBy('created_at', 'asc')->with('user'),
            'comments.replies' => fn($q) => $q->orderBy('created_at', 'asc')->with('user'),
        ]);

        if ($this->activeTab === 'replies') {
            $query->whereHas('comments', fn($q) => $q->where('user_id', $user->id))
                  ->where('user_id', '!=', $user->id);
        } elseif ($this->activeTab === 'saved') {
            $query->whereHas('saves', fn($q) => $q->where('user_id', $user->id));
        } else {
            $query->where('user_id', $user->id);
        }

        $userPosts = $query->latest()->paginate(10);

        $badgeClasses = [
            'bg-primary-50 text-primary-700',
            'bg-orange-50 text-orange-700',
            'bg-yellow-50 text-yellow-700',
            'bg-blue-50 text-blue-700',
            'bg-green-50 text-green-700',
            'bg-purple-50 text-purple-700',
        ];

        return view('livewire.admin.members-show', compact(
            'postsCount', 'hugsReceived', 'repliesReceived', 'savedCount',
            'userPosts', 'badgeClasses', 'categories'
        ));
    }
}; ?>

@section('title', 'Member Profile · ' . $user->name)

<div x-data="{
        showSlideshow: false,
        currentSlide: 0,
        slides: [],
        showEditPostModal: false,
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

    <!-- ===== PROFILE HEADER ===== -->
    <header class="pb-8 bg-white border border-gray-100 -mt-6 pt-10 rounded-2xl shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-8">

                <!-- Dynamic Avatar -->
                <div class="shrink-0">
                    @if ($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-32 h-32 md:w-36 md:h-36 rounded-full object-cover shadow-sm ring-4 ring-primary-50">
                    @else
                        <div class="w-32 h-32 md:w-36 md:h-36 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-5xl font-bold shadow-sm ring-4 ring-primary-50">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 justify-center md:justify-start">
                        {{ $user->name }}
                        @if($user->isAdmin())
                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">Admin</span>
                        @elseif($user->isModerator())
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">Moderator</span>
                        @endif
                    </h1>
                    <p class="text-gray-500 font-medium">{{ '@' . $user->username }}</p>
                    <p class="text-sm text-gray-400 mt-2 flex items-center justify-center md:justify-start">
                        <svg class="h-4 w-4 mr-1.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Joined {{ $user->created_at->format('F Y') }}
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('admin.members') }}" class="w-full md:w-auto border-2 border-gray-200 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-50 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Members
                    </a>
                </div>
            </div>

            <!-- Dynamic Stats Bar -->
            <div class="flex flex-wrap gap-6 md:gap-8 mt-8 pt-6 border-t border-gray-100 text-sm justify-center md:justify-start">
                <button wire:click="set('activeTab', 'posts')" class="flex items-center hover:text-primary-600 transition">
                    <span class="font-bold text-gray-900 text-lg mr-1.5 {{ $activeTab === 'posts' ? 'text-primary-600' : '' }}">{{ $postsCount }}</span>
                    <span class="text-gray-500">Posts</span>
                </button>
                <div class="flex items-center"><span class="font-bold text-gray-900 text-lg mr-1.5">{{ $hugsReceived }}</span> <span class="text-gray-500">Hugs Received</span></div>
                <button wire:click="set('activeTab', 'saved')" class="flex items-center hover:text-primary-600 transition">
                    <span class="font-bold text-gray-900 text-lg mr-1.5 {{ $activeTab === 'saved' ? 'text-primary-600' : '' }}">{{ $savedCount }}</span>
                    <span class="text-gray-500">Saved</span>
                </button>
            </div>
        </div>
    </header>

    <!-- ===== MAIN LAYOUT ===== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT SIDEBAR -->
            <aside class="hidden lg:block lg:col-span-3">
                <div class="space-y-6 sticky top-24">
                    <!-- Profile About -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">About {{ explode(' ', $user->name)[0] }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $user->signature ?? 'No bio provided yet.' }}</p>
                        <div class="space-y-3 text-sm text-gray-500 pt-4 border-t border-gray-50">
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg> {{ $user->city ?? 'N/A' }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg> {{ $user->child_name ? $user->child_name . ' (' . $user->child_age . ')' : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Admin Account Details Box -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Account Details</h3>
                        <div class="space-y-3 text-sm text-gray-500 pt-4 border-t border-gray-50">
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg> {{ $user->email }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg> {{ $user->phone ?? 'N/A' }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg> {{ $user->address ?? 'N/A' }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 3l5.553-2.776A1 1 0 0021 5.618v10.764a1 1 0 01-.553.894L15 20m-6-10l6-3m0 0V4m0 3l6-3" /></svg> {{ $user->postcode ?? 'N/A' }}</p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.948 11.948 0 0112 0C5.373 0 0 5.373 0 12s5.373 12 12 12c2.972 0 5.689-1.083 7.778-2.868" /></svg> Role: <span class="font-semibold text-primary-600 capitalize ml-1">{{ $user->role }}</span></p>
                            <p class="flex items-center"><svg class="h-4 w-4 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.948 11.948 0 0112 0C5.373 0 0 5.373 0 12s5.373 12 12 12c2.972 0 5.689-1.083 7.778-2.868" /></svg> Status: <span class="font-semibold text-{{ $user->status === 'banned' ? 'red' : 'green' }}-600 capitalize ml-1">{{ $user->status }}</span></p>
                        </div>
                    </div>

                    <!-- Admin Actions Box -->
                    <div class="bg-red-50 border border-red-100 rounded-2xl p-6 space-y-4">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <h3 class="font-bold text-red-800 text-sm">Admin Actions</h3>
                        </div>
                        
                        @if($canManageUser)
                            <div class="flex flex-wrap gap-2">
                                @if($user->status === 'banned')
                                    <button wire:click="updateUserStatus('approved')" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition">Unban User</button>
                                @else
                                    <button wire:click="updateUserStatus('banned')" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition">Ban User</button>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    @if($user->role !== 'admin')
                                        <button wire:click="updateUserRole('admin')" class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition">Make Admin</button>
                                    @endif
                                    @if($user->role !== 'moderator')
                                        <button wire:click="updateUserRole('moderator')" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">Make Moderator</button>
                                    @endif
                                    @if($user->role !== 'user')
                                        <button wire:click="updateUserRole('user')" class="px-3 py-1.5 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700 transition">Demote to User</button>
                                    @endif
                                @endif
                            </div>
                        @else
                            <p class="text-red-700 text-xs leading-relaxed">You do not have permission to modify this user's account.</p>
                        @endif
                    </div>
                </div>
            </aside>

            <!-- MAIN FEED -->
            <main class="lg:col-span-9">
                <!-- Gliding Tabs -->
                <div id="profile-tab-container" class="flex space-x-1 bg-white p-1 rounded-full border border-gray-100 shadow-sm w-fit mb-6 relative">
                    <div id="profile-tab-slider" wire:ignore class="absolute top-1 bottom-1 rounded-full bg-gray-900 shadow-sm transition-all duration-300 ease-out"></div>

                    <button wire:click="set('activeTab', 'posts')" class="relative z-10 px-6 py-2 text-sm font-medium rounded-full transition-colors {{ $activeTab === 'posts' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Posts</button>
                    <button wire:click="set('activeTab', 'replies')" class="relative z-10 px-6 py-2 text-sm font-medium rounded-full transition-colors {{ $activeTab === 'replies' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Replies</button>
                    <button wire:click="set('activeTab', 'saved')" class="relative z-10 px-6 py-2 text-sm font-medium rounded-full transition-colors {{ $activeTab === 'saved' ? 'text-white' : 'text-gray-500 hover:text-gray-900' }}">Saved</button>
                </div>

                <div class="space-y-6">
                    @forelse($userPosts as $post)
                        @php
                            $badgeClass = $badgeClasses[$post->category->id % count($badgeClasses)];
                            $hasHugged = $post->hugs->isNotEmpty();
                            $hasSaved = $post->saves->isNotEmpty();
                            $postTime = $post->created_at->diffInSeconds() < 60 ? 'Just now' : $post->created_at->diffForHumans();

                            // Prepare media for Alpine Slideshow
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

                            // Comment limit logic
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
                        <div class="post-card block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300" wire:key="post-{{ $post->id }}">
                            <div class="flex items-center mb-4">
                                @if ($post->user->profile_photo_path)
                                    <img src="{{ Storage::url($post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold mr-3">
                                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $post->user->name }}</p>
                                    <p class="text-xs text-gray-400">posted {{ $postTime }}</p>
                                </div>
                                <span class="ml-auto text-xs {{ $badgeClass }} px-3 py-1 rounded-full font-medium">{{ $post->category->icon }} {{ $post->category->name }}</span>
                            </div>

                            <h2 class="post-title text-xl font-bold text-gray-900 mb-2 transition flex items-center gap-2">
                                {{ $post->title }}
                                @if($post->status === 'hidden')
                                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full font-medium">Hidden</span>
                                @endif
                            </h2>

                            <!-- Formatted Post Body with See More -->
                            <div x-data="{ expanded: false }" class="text-gray-600 text-sm mb-4">
                                <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{{ $post->body }}</div>
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

                                            <!-- +More Overlay on 4th item -->
                                            @if ($index === 3 && $totalCount > 4)
                                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-2xl">
                                                    +{{ $totalCount - 3 }} more
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center text-sm text-gray-400 pt-4 border-t border-gray-50">
                                <!-- Reply Button -->
                                <button wire:click="openReplyForm('post-{{ $post->id }}')" class="flex items-center mr-6 hover:text-primary-600 transition cursor-pointer focus:outline-none">
                                    <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    Reply
                                </button>

                                <button wire:click="toggleHug({{ $post->id }})" class="flex items-center mr-6 transition cursor-pointer focus:outline-none">
                                    <svg class="h-5 w-5 mr-1.5 {{ $hasHugged ? 'text-orange-500 fill-orange-500' : 'text-gray-400 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    <span class="{{ $hasHugged ? 'text-orange-500 font-medium' : '' }}">{{ $post->hugs_count }} Hugs</span>
                                </button>

                                <!-- Save / Bookmark Button -->
                                <button wire:click="toggleSavePost({{ $post->id }})" class="flex items-center mr-6 transition cursor-pointer focus:outline-none">
                                    <svg class="h-5 w-5 mr-1.5 {{ $hasSaved ? 'text-primary-500 fill-primary-500' : 'text-gray-400 fill-none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                    <span class="{{ $hasSaved ? 'text-primary-500 font-medium' : '' }}">Save</span>
                                </button>

                                @if($post->user_id == auth()->id())
                                    <button wire:click="openEditModal({{ $post->id }})" class="flex items-center mr-6 transition cursor-pointer focus:outline-none">
                                        <svg class="h-5 w-5 mr-1.5 text-gray-400 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Edit
                                    </button>

                                    <button wire:click="deletePost({{ $post->id }})" onclick="return confirm('Are you sure you want to delete this post? This cannot be undone.')" class="flex items-center mr-6 transition cursor-pointer focus:outline-none">
                                        <svg class="h-5 w-5 mr-1.5 text-gray-400 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete
                                    </button>
                                @endif

                                <!-- ADMIN/MOD HIDE/DELETE BUTTONS -->
                                @if($canManageUser)
                                    <div class="flex items-center ml-auto">
                                        @if($post->status === 'hidden')
                                            <button wire:click="unhidePost({{ $post->id }})" class="flex items-center mr-4 transition cursor-pointer focus:outline-none" onclick="return confirm('Make this post public again?')">
                                                <svg class="h-5 w-5 mr-1.5 text-blue-500 hover:text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <span class="text-blue-500 font-medium text-xs">Unhide</span>
                                            </button>
                                        @else
                                            <button wire:click="hidePost({{ $post->id }})" class="flex items-center mr-4 transition cursor-pointer focus:outline-none" onclick="return confirm('Hide this post from public view?')">
                                                <svg class="h-5 w-5 mr-1.5 text-yellow-500 hover:text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                <span class="text-yellow-600 font-medium text-xs">Hide Post</span>
                                            </button>
                                        @endif

                                        <button wire:click="deletePostAdmin({{ $post->id }})" onclick="return confirm('PERMANENTLY DELETE this post? This cannot be undone.')" class="flex items-center transition cursor-pointer focus:outline-none">
                                            <svg class="h-5 w-5 mr-1.5 text-red-500 hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span class="text-red-500 font-medium text-xs">Delete</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- ===== COMMENTS SECTION ===== -->
                            <div class="mt-4 space-y-4">

                                @if($activeReplyForm === 'post-' . $post->id)
                                    <form wire:submit="postComment({{ $post->id }})" class="flex flex-col space-y-2">
                                        <textarea wire:model="replyText" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Write a reply..."></textarea>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" wire:click="closeReplyForm" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                            <button type="submit" class="px-4 py-1 text-xs text-white bg-primary-600 rounded-lg hover:bg-primary-700">Post Reply</button>
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
                                        <div class="flex-1">
                                            <div class="bg-gray-50 rounded-xl px-3 py-2 inline-block">
                                                <p class="text-xs font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                <div x-data="{ expanded: false }" class="text-sm text-gray-700 mt-1">
                                                    <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{{ $comment->body }}</div>
                                                    @if($isLongComment)
                                                        <button @click="expanded = !expanded" class="text-primary-600 font-medium hover:text-primary-500 transition cursor-pointer focus:outline-none text-xs mt-1">
                                                            <span x-show="!expanded">See more</span>
                                                            <span x-show="expanded" x-cloak>See less</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center mt-1 ml-1">
                                                <p class="text-xs text-gray-400 mr-3">{{ $commentTime }}</p>
                                                <button wire:click="openReplyForm('comment-{{ $comment->id }}', {{ json_encode($comment->user->name) }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium">Reply</button>
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
                                                <div class="ml-10 pl-4 border-l border-gray-100 space-y-3 mt-3" wire:key="replies-{{ $comment->id }}">
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
                                                            <div class="flex-1">
                                                                <div class="bg-gray-50 rounded-xl px-3 py-2 inline-block">
                                                                    <p class="text-xs font-semibold text-gray-900">{{ $reply->user->name }}</p>
                                                                    <div x-data="{ expanded: false }" class="text-sm text-gray-700 mt-1">
                                                                        <div :class="expanded ? '' : 'line-clamp-3'" style="white-space: pre-wrap;">{{ $reply->body }}</div>
                                                                        @if($isLongReply)
                                                                            <button @click="expanded = !expanded" class="text-primary-600 font-medium hover:text-primary-500 transition cursor-pointer focus:outline-none text-xs mt-1">
                                                                                <span x-show="!expanded">See more</span>
                                                                                <span x-show="expanded" x-cloak>See less</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-center mt-1 ml-1">
                                                                    <p class="text-xs text-gray-400 mr-3">{{ $replyTime }}</p>
                                                                    <button wire:click="openReplyForm('comment-{{ $comment->id }}', {{ json_encode($reply->user->name) }})" class="text-xs text-gray-500 hover:text-primary-600 font-medium">Reply</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if($activeReplyForm === 'comment-' . $comment->id)
                                                        <form wire:submit="postComment({{ $post->id }}, {{ $comment->id }})" class="flex flex-col space-y-2 mt-2">
                                                            <textarea wire:model="replyText" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Write a reply..."></textarea>
                                                            <div class="flex justify-end space-x-2">
                                                                <button type="button" wire:click="closeReplyForm" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                                <button type="submit" class="px-4 py-1 text-xs text-white bg-primary-600 rounded-lg hover:bg-primary-700">Post Reply</button>
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
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                            @if($activeTab === 'posts')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p>This user hasn't posted anything yet.</p>
                            @elseif($activeTab === 'replies')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                <p>This user hasn't replied to any discussions yet.</p>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                <p>No saved posts yet.</p>
                            @endif
                        </div>
                    @endforelse
                </div>

                <div class="text-center mt-8">
                    {{ $userPosts->links() }}
                </div>
            </main>
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

        <!-- Next Button -->
        <button @click="nextSlide()" x-show="slides.length > 1" class="absolute right-4 sm:right-6 text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition z-20">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>

        <!-- Counter -->
        <div x-show="slides.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white bg-black/40 px-4 py-1 rounded-full text-sm z-20">
            <span x-text="currentSlide + 1"></span> / <span x-text="slides.length"></span>
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
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
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
                                            <div class="relative group">
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
                                                <button type="button" wire:click="removeExistingMedia({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md opacity-0 group-hover:opacity-100 transition focus:outline-none">
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

                                @if(!empty($editUploads))
                                    <div class="mt-3 grid grid-cols-3 gap-3">
                                        @foreach($editUploads as $file)
                                            <div class="relative group">
                                                <div class="rounded-lg overflow-hidden bg-gray-100 border border-gray-200 aspect-square flex items-center justify-center">
                                                    @if (str_starts_with($file->getMimeType(), 'image/'))
                                                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover">
                                                    @else
                                                        <video src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover bg-black" controls preload="metadata"></video>
                                                    @endif
                                                </div>
                                                <button type="button" wire:click="removeEditUpload({{ $loop->index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md opacity-0 group-hover:opacity-100 transition focus:outline-none">
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
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
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

    <script>
        // Function to align the profile tab slider to the active button
        function alignProfileTabSlider() {
            const slider = document.getElementById('profile-tab-slider');
            if (!slider) return;

            const activeBtn = document.querySelector('#profile-tab-container button.text-white');
            if (activeBtn) {
                slider.style.left = activeBtn.offsetLeft + 'px';
                slider.style.width = activeBtn.offsetWidth + 'px';
            }
        }

        alignProfileTabSlider();
        window.addEventListener('resize', alignProfileTabSlider);
        document.addEventListener('livewire:navigated', alignProfileTabSlider);
        window.addEventListener('load', alignProfileTabSlider);

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#profile-tab-container button');
            if (btn) {
                const slider = document.getElementById('profile-tab-slider');
                if (slider) {
                    slider.style.left = btn.offsetLeft + 'px';
                    slider.style.width = btn.offsetWidth + 'px';
                }
            }
        });
    </script>

</div>