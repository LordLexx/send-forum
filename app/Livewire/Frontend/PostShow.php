<?php
namespace App\Livewire\Frontend;

use App\Models\Post;
use App\Models\Category;
use App\Models\Event;
use App\Models\Hug;
use App\Models\Comment;
use App\Models\SavedPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.forum-layout')] class extends Component {
    use WithFileUploads;

    public Post $post;
    public $categories;
    public $nextEvent;

    public $badgeClasses = [
        'bg-teal-50 text-teal-700',
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

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->categories = Category::where('status', 'active')->orderBy('name')->get();
        $this->nextEvent = Event::where('status', 'published')->where('start_date', '>=', now())->orderBy('start_date', 'asc')->first();
    }

    public function toggleComments($postId, $totalComments = 0): void { /* Same logic as feed */
        $currentState = $this->commentStates[$postId] ?? 'default';
        if ($currentState === 'default') $this->commentStates[$postId] = $totalComments > 1 ? 'expanded' : 'hidden';
        elseif ($currentState === 'expanded') $this->commentStates[$postId] = 'hidden';
        else $this->commentStates[$postId] = 'default';
    }
    public function toggleReplies($commentId): void {
        if (in_array($commentId, $this->expandedReplies)) $this->expandedReplies = array_diff($this->expandedReplies, [$commentId]);
        else $this->expandedReplies[] = $commentId;
    }
    public function removeEditUpload($index): void {
        if (isset($this->editUploads[$index])) { unset($this->editUploads[$index]); $this->editUploads = array_values($this->editUploads); }
    }
    public function toggleHug($postId): void {
        $post = Post::find($postId); if (!$post) return;
        $existingHug = Hug::where('post_id', $postId)->where('user_id', Auth::id())->first();
        if ($existingHug) { $existingHug->delete(); $post->decrement('hugs_count'); }
        else { Hug::create(['post_id' => $postId, 'user_id' => Auth::id()]); $post->increment('hugs_count'); }
    }
    public function toggleSavePost($postId): void {
        $existing = SavedPost::where('user_id', Auth::id())->where('post_id', $postId)->first();
        if ($existing) $existing->delete();
        else SavedPost::create(['user_id' => Auth::id(), 'post_id' => $postId]);
    }
    public function openReplyForm($formId, $replyToName = null): void {
        $this->activeReplyForm = $formId;
        $this->replyText = $replyToName ? '@' . preg_replace('/\s+/', '', $replyToName) . ' ' : '';
    }
    public function closeReplyForm(): void { $this->activeReplyForm = null; $this->replyText = ''; }
    public function postComment($postId, $parentId = null): void {
        $this->validate(['replyText' => 'required|string|max:1000']);
        Comment::create(['post_id' => $postId, 'user_id' => Auth::id(), 'parent_id' => $parentId, 'body' => $this->replyText]);
        $post = Post::find($postId); $post->increment('replies_count');
        $this->reset(['replyText', 'activeReplyForm']);
    }
    public function openEditModal($postId): void {
        $post = Post::find($postId);
        if ($post && $post->user_id == Auth::id()) {
            $this->editPostId = $post->id; $this->editTitle = $post->title; $this->editBody = $post->body;
            $this->editCategoryId = $post->category_id; $this->editMedia = $post->media ?? []; $this->editUploads = [];
            $this->dispatch('open-edit-modal');
        }
    }
    public function removeExistingMedia($index): void {
        if (isset($this->editMedia[$index])) { unset($this->editMedia[$index]); $this->editMedia = array_values($this->editMedia); }
    }
    public function updatePost(): void {
        $post = Post::find($this->editPostId);
        if ($post && $post->user_id == Auth::id()) {
            $this->validate([
                'editTitle' => 'required|string|max:255', 'editBody' => 'required|string',
                'editCategoryId' => 'required|exists:categories,id', 'editUploads' => 'nullable|array|max:10',
                'editUploads.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:51200',
            ]);
            $originalMedia = $post->media ?? []; $keptMedia = $this->editMedia;
            foreach ($originalMedia as $oldPath) { if (!in_array($oldPath, $keptMedia)) Storage::disk('public')->delete($oldPath); }
            $newMediaPaths = [];
            if (!empty($this->editUploads)) { foreach ($this->editUploads as $file) $newMediaPaths[] = $file->store('post-media', 'public'); }
            $post->update(['title' => $this->editTitle, 'body' => $this->editBody, 'category_id' => $this->editCategoryId, 'media' => array_merge($keptMedia, $newMediaPaths)]);
            $this->reset(['editPostId', 'editTitle', 'editBody', 'editCategoryId', 'editMedia', 'editUploads']);
            Session::flash('success', 'Post updated successfully!');
            $this->redirect(route('posts.show', $post));
        }
    }
    public function deletePost($postId): void {
        $post = Post::find($postId);
        if ($post && $post->user_id == Auth::id()) {
            if (!empty($post->media)) { foreach ($post->media as $mediaPath) Storage::disk('public')->delete($mediaPath); }
            $post->delete(); Session::flash('success', 'Post deleted successfully!');
            $this->redirect(route('feed'));
        }
    }

    public function render(): mixed
    {
        // Eager load relations for the single post
        $this->post->load([
            'user', 'category',
            'hugs' => fn($q) => $q->where('user_id', auth()->id()),
            'saves' => fn($q) => $q->where('user_id', auth()->id()),
            'comments' => fn($q) => $q->orderBy('created_at', 'asc')->with('user'),
            'comments.replies' => fn($q) => $q->orderBy('created_at', 'asc')->with('user'),
        ]);

        return view('livewire.frontend.post-show', [
            'categories' => $this->categories,
            'nextEvent' => $this->nextEvent,
            'badgeClasses' => $this->badgeClasses
        ]);
    }
}; ?>
