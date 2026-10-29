<?php
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.forum-layout')] class extends Component {
    use WithPagination;

    public $search = '';

    // Reset pagination when search is updated
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        // Fetch active categories, filter by search, paginated
        $categories = Category::where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.frontend.all-categories', [
            'categories' => $categories
        ]);
    }
}; ?>

<div>
    @section('title', 'All Categories')

    <!-- ===== MAIN LAYOUT ===== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8 text-center">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Community Categories</h1>
            <p class="text-gray-500 mt-2 max-w-2xl mx-auto text-sm md:text-base">
                Browse all the discussion areas available in our community. Find support, share experiences, and connect with others who understand.
            </p>

            <!-- Search Bar -->
            <div class="mt-6 max-w-xl mx-auto relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search categories by name or topic..."
                    class="block w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
                <a href="{{ route('category', $category) }}" wire:navigate
                   class="group block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-primary-200 transition duration-300 relative overflow-hidden">

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-4xl">{{ $category->icon }}</span>
                            <span class="text-xs font-medium text-gray-500 bg-gray-50 px-2.5 py-1 rounded-full border border-gray-100">
                                {{ $category->posts_count }} {{ $category->posts_count === 1 ? 'Post' : 'Posts' }}
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition">
                            {{ $category->name }}
                        </h2>

                        <p class="text-sm text-gray-500 line-clamp-3 mb-5 min-h-[60px]">
                            {{ $category->description }}
                        </p>

                        <div class="flex items-center text-primary-600 text-sm font-medium">
                            Enter Category
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500">No categories found matching "{{ $search }}".</p>
                    <button wire:click="$set('search', '')" class="mt-4 text-primary-600 text-sm font-medium hover:text-primary-500 transition">
                        Clear Search
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="mt-8">
                {{ $categories->links() }}
            </div>
        @endif

    </div>
</div>