<?php
use App\Models\Post;
use App\Models\PostReport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
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
            $this->dispatch('notify', type: 'success', message: "Reports dismissed for post.");
        }
    }

    public function render(): mixed
    {
        // Get top 5 reported posts for the moderation queue
        $reportedPosts = Post::whereHas('reports')
            ->withCount('reports')
            ->with('user', 'category')
            ->latest()
            ->take(5)
            ->get();

        // Get all posts for the table
        $posts = Post::with('user', 'category', 'reports')
            ->when($this->statusFilter === 'published', fn($q) => $q->where('status', 'published')->whereDoesntHave('reports'))
            ->when($this->statusFilter === 'hidden', fn($q) => $q->where('status', 'hidden'))
            ->when($this->statusFilter === 'reported', fn($q) => $q->whereHas('reports'))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('body', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.posts', compact('reportedPosts', 'posts'));
    }
}; ?>

@section('page_title', 'Posts & Moderation')

<div class="space-y-8">

    <!-- ===== Moderation Queue ===== -->
    @if($reportedPosts->isNotEmpty())
    <section>
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Reported Content Queue
        </h2>

        <div class="space-y-4">
            @foreach($reportedPosts as $rPost)
                <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start space-x-4">
                        <div class="shrink-0">
                            @if ($rPost->user && $rPost->user->profile_photo_path)
                                <img src="{{ Storage::url($rPost->user->profile_photo_path) }}" alt="{{ $rPost->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm">
                                    {{ strtoupper(substr($rPost->user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded-full font-semibold uppercase">reported</span>
                                <span class="text-xs text-gray-500">{{ $rPost->reports_count }} Active Reports</span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base">"{{ \Illuminate\Support\Str::limit($rPost->title, 50) }}"</h3>
                            <p class="text-sm text-gray-600 mt-1">Post by
                                @if($rPost->user)
                                    <a href="{{ route('admin.members.show', $rPost->user) }}" wire:navigate class="font-medium text-primary-600 hover:underline">{{ $rPost->user->name }}</a>
                                @else
                                    <span class="font-medium">Unknown</span>
                                @endif
                                in <span class="font-medium">{{ $rPost->category->icon ?? '📋' }} {{ $rPost->category->name ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2 shrink-0">
                        <a href="{{ route('admin.posts.show', $rPost) }}" wire:navigate class="flex-1 md:flex-none bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition text-center">View</a>
                        {{-- Security: Hide action buttons if moderator is viewing a staff member's post --}}
                        @if(!auth()->user()->isModerator() || !$rPost->user->isStaff())
                            <button wire:click="hidePost({{ $rPost->id }})" class="flex-1 md:flex-none bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">Hide Post</button>
                            <button wire:click="dismissReport({{ $rPost->id }})" class="flex-1 md:flex-none bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Dismiss</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- ===== All Posts Table ===== -->
    <section>
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            All Community Posts
        </h2>

        <!-- Controls -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div class="relative w-full md:max-w-xs">
                <input type="text" wire:model.live="search" placeholder="Search posts..." class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <div id="filter-container" class="flex space-x-1 bg-gray-50 p-1 rounded-full border border-gray-100 w-full md:w-auto justify-between md:justify-start relative">
                <div id="filter-slider" wire:ignore class="absolute top-1 bottom-1 rounded-full bg-white shadow-sm transition-all duration-300 ease-out"></div>

                <button wire:click="set('statusFilter', 'all')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'all' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                <button wire:click="set('statusFilter', 'published')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'published' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Safe</button>
                <button wire:click="set('statusFilter', 'hidden')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'hidden' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Hidden</button>
                <button wire:click="set('statusFilter', 'reported')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'reported' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Reported</button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Post Title</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($posts as $post)
                            <tr wire:key="post-{{ $post->id }}" class="hover:bg-gray-50 transition {{ $post->status == 'hidden' ? 'opacity-60' : '' }} {{ $post->reports->isNotEmpty() ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</div>
                                    <div class="text-xs text-gray-400">{{ $post->replies_count }} Replies</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($post->user && $post->user->profile_photo_path)
                                            <img src="{{ Storage::url($post->user->profile_photo_path) }}" alt="{{ $post->user->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs mr-2">
                                                {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        @if($post->user)
                                            <a href="{{ route('admin.members.show', $post->user) }}" wire:navigate class="text-sm text-gray-600 hover:text-primary-600 transition">{{ $post->user->name }}</a>
                                        @else
                                            <span class="text-sm text-gray-600">Unknown</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $post->category->icon ?? '📋' }} {{ $post->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($post->reports->isNotEmpty())
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">Reported</span>
                                    @elseif($post->status == 'hidden')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">Hidden</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">Published</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.posts.show', $post) }}" wire:navigate class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 text-gray-600 bg-white rounded-md text-xs font-medium hover:bg-gray-50 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            View
                                        </a>
                                        {{-- Security: Hide action buttons if moderator is viewing a staff member's post --}}
                                        @if(!auth()->user()->isModerator() || !$post->user->isStaff())
                                            @if($post->status == 'published')
                                                <button wire:click="hidePost({{ $post->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-orange-200 text-orange-700 bg-orange-50 rounded-md text-xs font-medium hover:bg-orange-100 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                                    Hide
                                                </button>
                                            @else
                                                <button wire:click="restorePost({{ $post->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-primary-200 text-primary-700 bg-primary-50 rounded-md text-xs font-medium hover:bg-primary-100 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    Restore
                                                </button>
                                            @endif
                                            <button wire:click="deletePost({{ $post->id }})" wire:confirm="Are you sure you want to permanently delete this post?" class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">No posts found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($posts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>

    <script>
        function alignFilterSlider() {
            const slider = document.getElementById('filter-slider');
            if (!slider) return;
            const activeBtn = document.querySelector('#filter-container button.text-gray-900');
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
    </script>
</div>