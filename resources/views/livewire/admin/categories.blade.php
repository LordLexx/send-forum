<?php
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            $name = $category->name;
            $category->delete();
            $this->dispatch('notify', type: 'error', message: "{$name} has been deleted.");
        }
    }

    public function setStatus($categoryId, $status)
    {
        $category = Category::find($categoryId);
        if ($category) {
            $category->status = $status;
            $category->save();

            $message = $status === 'locked' ? "{$category->name} has been locked." : "{$category->name} has been unlocked.";
            $this->dispatch('notify', type: 'success', message: $message);
        }
    }

    public function togglePriority($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) return;

        // If we are trying to ENABLE priority, check the limit first
        if (!$category->make_priority) {
            $count = Category::where('make_priority', true)->count();
            if ($count >= 15) {
                $this->dispatch('notify', type: 'error', message: 'Limit of 15 priority categories reached. Please disable priority on another category first.');
                return;
            }
        }

        $category->make_priority = !$category->make_priority;
        $category->save();

        $message = $category->make_priority
            ? "{$category->name} is now shown on the frontend feed."
            : "{$category->name} is no longer shown on the frontend feed.";

        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function render(): mixed
    {
        // Eager load the post counts to prevent N+1 queries
        $categories = Category::withCount('posts')->orderBy('created_at', 'desc')->paginate(15);
        return view('livewire.admin.categories', compact('categories'));
    }
}; ?>

@section('page_title', 'Manage Categories')

<div class="space-y-6">
    <!-- Info Banner -->
    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 flex items-start">
        <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <h3 class="font-semibold text-primary-800 text-sm">How Categories Work</h3>
            <p class="text-primary-700 text-xs mt-1">Categories organize all discussions on the forum. If you delete a
                category, you will be asked to move its existing posts to another category before deletion.</p>
        </div>
    </div>

    <!-- Controls -->
    <div class="flex justify-end">
        <a href="{{ route('admin.categories.create') }}"
            class="flex items-center bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition shadow-sm">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Category Name</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">
                            Description</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Posts
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr wire:key="category-{{ $category->id }}"
                            class="hover:bg-gray-50 transition {{ $category->status == 'locked' ? 'opacity-80' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3 shrink-0">{{ $category->icon }}</span>
                                    <!-- Added max-w and truncate to the name -->
                                    <div class="text-sm font-medium text-gray-900 max-w-[150px] truncate flex items-center gap-2">
                                        {{ $category->name }}
                                        @if($category->make_priority)
                                            <svg class="w-4 h-4 text-yellow-400 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <!-- Added truncate to the description -->
                            <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-500 max-w-xs truncate">
                                {{ $category->description }}
                            </td>
                            <!-- Displaying actual posts count -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->posts_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->status == 'active')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                                @elseif($category->status == 'locked')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">Locked</span>
                                @elseif($category->status == 'hidden')
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">

                                    <!-- Priority Toggle Button -->
                                    @if($category->make_priority)
                                        <button wire:click="togglePriority({{ $category->id }})"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-yellow-200 text-yellow-700 bg-yellow-50 rounded-md text-xs font-medium hover:bg-yellow-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Unprioritize
                                        </button>
                                    @else
                                        <button wire:click="togglePriority({{ $category->id }})"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 text-gray-600 bg-white rounded-md text-xs font-medium hover:bg-gray-50 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Make Priority
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 text-gray-600 bg-white rounded-md text-xs font-medium hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    @if($category->status == 'locked')
                                        <button wire:click="setStatus({{ $category->id }}, 'active')"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-primary-200 text-primary-700 bg-primary-50 rounded-md text-xs font-medium hover:bg-primary-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 11V7a4 4 0 118 0m-4 8v2a2 2 0 11-4 0v-2a2 2 0 014 0z" />
                                            </svg>
                                            Unlock
                                        </button>
                                    @else
                                        <button wire:click="setStatus({{ $category->id }}, 'locked')"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-orange-200 text-orange-700 bg-orange-50 rounded-md text-xs font-medium hover:bg-orange-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Lock
                                        </button>
                                    @endif

                                    <button wire:click="deleteCategory({{ $category->id }})"
                                        wire:confirm="Are you sure you want to delete this category?"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No categories found. Create one to
                                get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>