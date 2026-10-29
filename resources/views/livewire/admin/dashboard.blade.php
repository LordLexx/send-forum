<?php
use App\Models\User;
use App\Models\Post;
use App\Models\PostReport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-layout')] class extends Component {
    public $totalMembers;
    public $pendingUsers;
    public $activeDiscussionsCount;
    public $reportedPosts;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        $this->totalMembers = User::where('is_admin', false)->where('status', 'approved')->count();
        $this->pendingUsers = User::where('is_admin', false)->where('status', 'pending')->latest()->get();

        // Active Discussions: Posts that have received comments in the past 24 hours
        $this->activeDiscussionsCount = Post::where('status', 'published')
            ->whereHas('comments', function ($query) {
                $query->where('comments.created_at', '>=', now()->subDay());
            })
            ->count();

        // Reported Posts: Posts that have at least one pending report
        $this->reportedPosts = Post::whereHas('reports')
            ->with(['user', 'category', 'reports'])
            ->withCount('reports')
            ->latest()
            ->get();
    }

    public function approveUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->status = 'approved';
            $user->save();

            $this->dispatch('notify', type: 'success', message: "{$user->name} has been approved.");
        }
        $this->updateStats();
    }

    public function rejectUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->status = 'rejected';
            $user->save();

            $this->dispatch('notify', type: 'error', message: "{$user->name} has been rejected.");
        }
        $this->updateStats();
    }

    public function dismissReports($postId)
    {
        PostReport::where('post_id', $postId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Reports have been dismissed.');
        $this->updateStats();
    }

    public function hidePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            $post->status = 'hidden';
            $post->save();
            PostReport::where('post_id', $postId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Post has been hidden and reports cleared.');
        }
        $this->updateStats();
    }
}; ?>

@section('page_title', 'Dashboard')

<div class="space-y-8">
    <!-- Welcome Message -->
    <div
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }} 👋</h2>
            <p class="text-gray-500 mt-1">You have <span
                    class="font-semibold text-orange-600">{{ $pendingUsers->count() }} pending member approval{{ $pendingUsers->count() != 1 ? 's' : '' }}</span>
                and <span class="font-semibold text-red-600">{{ $reportedPosts->count() }} reported post{{ $reportedPosts->count() != 1 ? 's' : '' }}</span> to review.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Live</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $totalMembers }}</h3>
            <p class="text-gray-500 text-sm mt-1">Total Members</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Needs
                    Action</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $pendingUsers->count() }}</h3>
            <p class="text-gray-500 text-sm mt-1">Pending Approvals</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Live</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $activeDiscussionsCount }}</h3>
            <p class="text-gray-500 text-sm mt-1">Active Discussions <span class="text-xs text-gray-400">(24h)</span></p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">Urgent</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">{{ $reportedPosts->count() }}</h3>
            <p class="text-gray-500 text-sm mt-1">Reported Posts</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- ===== Pending Approvals ===== -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pending Member Approvals</h3>
                    <p class="text-sm text-gray-500">Verify Slough residency before approving.</p>
                </div>
                <a href="#" class="text-sm text-primary-600 hover:text-primary-800 font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-100">
                @if($pendingUsers->count() > 0)
                    @foreach($pendingUsers as $user)
                        <div wire:key="user-{{ $user->id }}"
                            class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }} · {{ $user->city }},
                                        {{ $user->postcode }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">Applied {{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2 w-full sm:w-auto shrink-0">
                                <button wire:click="approveUser({{ $user->id }})" wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600 transition flex items-center justify-center disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="approveUser({{ $user->id }})" class="h-4 w-4 mr-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg wire:loading wire:target="approveUser({{ $user->id }})"
                                        class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Approve
                                </button>
                                <button wire:click="rejectUser({{ $user->id }})" wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition flex items-center justify-center disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="rejectUser({{ $user->id }})" class="h-4 w-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <svg wire:loading wire:target="rejectUser({{ $user->id }})" class="animate-spin h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-500">No pending approvals. You're all caught up! 🎉</div>
                @endif
            </div>
        </div>

        <!-- ===== Reported Posts ===== -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Reported Posts</h3>
                    <p class="text-sm text-gray-500">Review reported content and take appropriate action.</p>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @if($reportedPosts->count() > 0)
                    @foreach($reportedPosts as $post)
                        @php
                            $lastReport = $post->reports->max('created_at');
                        @endphp
                        <div wire:key="reported-post-{{ $post->id }}"
                            class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-gray-50 transition">
                            <div class="flex items-start space-x-4 min-w-0 flex-1">
                                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold shrink-0">
                                    {{ strtoupper(substr($post->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-gray-900 truncate">{{ $post->title }}</p>
                                    <p class="text-sm text-gray-500 truncate">
                                        by {{ $post->user->name ?? 'Unknown' }}
                                        @if($post->category) · {{ $post->category->icon }} {{ $post->category->name }} @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 text-red-700 font-medium">
                                            {{ $post->reports_count }} report{{ $post->reports_count != 1 ? 's' : '' }}
                                        </span>
                                        @if($lastReport)
                                            · Last reported {{ $lastReport->diffForHumans() }}
                                        @endif
                                        · Posted {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2 w-full sm:w-auto shrink-0">
                                <a href="{{ route('admin.posts') }}" wire:navigate target="_blank"
                                    class="flex-1 sm:flex-none bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Moderate
                                </a>
                                <button wire:click="dismissReports({{ $post->id }})" wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600 transition flex items-center justify-center disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="dismissReports({{ $post->id }})" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg wire:loading wire:target="dismissReports({{ $post->id }})" class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Dismiss
                                </button>
                                <button wire:click="hidePost({{ $post->id }})" wire:loading.attr="disabled" wire:confirm="Are you sure you want to hide this post from the community?"
                                    class="flex-1 sm:flex-none bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition flex items-center justify-center disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="hidePost({{ $post->id }})" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.36 1.84c.32.12.48.51.36.83l-.5 1.41a9.662 9.662 0 014.294 5.92l1.4-.49a.677.677 0 11.43 1.28l-1.4.49c.262 1.65.07 3.35-.55 4.91l1.32.84a.677.677 0 11-.72 1.15l-1.13-.72a9.7 9.7 0 01-3.86 3.04l.46 1.41a.677.677 0 01-1.28.43l-.5-1.5a9.66 9.66 0 01-4.85.04l-.5 1.46a.677.677 0 11-1.28-.43l.43-1.27a9.662 9.662 0 01-3.79-2.99l-1.18.74a.677.677 0 11-.72-1.15l1.32-.83a9.66 9.66 0 01-.55-4.92l-1.4-.48a.677.677 0 11.43-1.28l1.4.48a9.662 9.662 0 014.29-5.91l-.5-1.4a.677.677 0 01.36-.83.677.677 0 01.86.36l.5 1.4a9.66 9.66 0 014.85-.04l.5-1.42a.677.677 0 01.83-.36z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    </svg>
                                    <svg wire:loading wire:target="hidePost({{ $post->id }})" class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Hide
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-500">No reported posts. The community is behaving well! 🎉</div>
                @endif
            </div>
        </div>
    </div>
</div>