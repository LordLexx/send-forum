<?php
// resources/views/livewire/admin/jargon-index.blade.php

use App\Models\JargonTerm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    // Named $filter (not $statusFilter) so it matches the events-page markup:
    // the template reads {{ $filter }} and the buttons call $set('filter', ...).
    #[Url(except: 'all')]
    public string $filter = 'all';

    public $confirmingDeleteId = null;
    public $confirmingDeleteTerm = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter = 'all';
        $this->resetPage();
    }

    public function confirmDelete($id): void
    {
        $term = JargonTerm::find($id);

        if ($term) {
            $this->confirmingDeleteId = $term->id;
            $this->confirmingDeleteTerm = $term->term;
        }
    }

    public function deleteTerm(): void
    {
        if ($this->confirmingDeleteId) {
            JargonTerm::where('id', $this->confirmingDeleteId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Term deleted successfully.');
        }

        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
        $this->confirmingDeleteTerm = null;
    }

    public function with(): array
    {
        $terms = JargonTerm::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('term', 'like', '%' . $this->search . '%')
                        ->orWhere('full_form', 'like', '%' . $this->search . '%')
                        ->orWhere('definition', 'like', '%' . $this->search . '%');
                });
            })
            // Buttons use 'drafts' (plural, same as the Events page) but the
            // status column stores 'draft' — map explicitly, or Drafts shows nothing.
            ->when($this->filter === 'published', fn ($query) => $query->where('status', 'published'))
            ->when($this->filter === 'drafts', fn ($query) => $query->where('status', 'draft'))
            ->orderBy('term')
            ->paginate(15);

        return compact('terms');
    }
};
?>

@section('page_title', 'Jargon Buster')

<div> <!-- Root element wrapper -->
    <div class="p-6 space-y-6">

        <!-- Page Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Jargon Buster Terms</h1>
                <p class="text-sm text-gray-500 mt-1">Manage the dictionary shown on the public Jargon Buster page.</p>
            </div>
        </div>

        <!-- Search & Status Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
            <div class="relative flex-1 max-w-xl">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search terms, full forms or definitions…"
                    class="w-full h-11 pl-11 pr-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Filters & Actions -->
            <div class="flex items-center space-x-3 w-full md:w-auto justify-between md:justify-end">
                <!-- Sliding filter (mirrors Events/Resources pages) -->
                <div id="filter-container"
                    class="flex space-x-1 bg-gray-50 p-1 rounded-full border border-gray-100 relative">
                    <div id="filter-slider" wire:ignore
                        class="absolute top-1 bottom-1 rounded-full bg-white shadow-sm transition-all duration-300 ease-out">
                    </div>
                    <button data-filter="all" wire:click="$set('filter', 'all')"
                        class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'all' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                    <button data-filter="published" wire:click="$set('filter', 'published')"
                        class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'published' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Published</button>
                    <button data-filter="drafts" wire:click="$set('filter', 'drafts')"
                        class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'drafts' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Drafts</button>
                </div>
                <a href="{{ route('admin.jargon.create') }}" wire:navigate
                    class="flex items-center bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600 transition shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Term
                </a>
            </div>
        </div>

        <!-- Terms Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($terms->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100 bg-gray-50/50">
                                <th class="px-6 py-4">Term</th>
                                <th class="px-6 py-4">Stands For</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Updated</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($terms as $term)
                                <tr wire:key="term-{{ $term->id }}" class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-bold text-primary-700">{{ $term->term }}</span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        @if($term->full_form)
                                            <span class="text-gray-600">{{ Str::limit($term->full_form, 50) }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($term->status === 'published')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Published
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ $term->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-1">
                                            @if($term->status === 'published')
                                                {{-- TODO: swap for route('jargon.show', $term->slug) once public routes exist --}}
                                                <a href="{{ route('jargon.show', $term->slug) }}" target="_blank" title="View public page"
                                                    class="p-2 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.jargon.edit', $term) }}" wire:navigate title="Edit term"
                                                class="p-2 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDelete({{ $term->id }})" title="Delete term"
                                                class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($terms->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $terms->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        {{ $search !== '' || $filter !== 'all' ? 'No terms match your filters' : 'No terms yet' }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        {{ $search !== '' || $filter !== 'all' ? 'Try a different search or clear the filters.' : 'Start building the dictionary your families will rely on.' }}
                    </p>
                    @if($search !== '' || $filter !== 'all')
                        <button wire:click="resetFilters"
                            class="inline-flex items-center px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                            Clear Filters
                        </button>
                    @else
                        <a href="{{ route('admin.jargon.create') }}" wire:navigate
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600 transition">
                            Add Your First Term
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        @if($confirmingDeleteId)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="cancelDelete"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 md:p-8">
                    <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete this term?</h3>
                    <p class="text-sm text-gray-500 text-center mb-7">
                        <span class="font-semibold text-gray-700">{{ $confirmingDeleteTerm }}</span> will be permanently removed from the Jargon Buster. This action cannot be undone.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="cancelDelete"
                            class="flex-1 h-11 rounded-lg border border-gray-200 text-gray-600 font-medium text-sm hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button wire:click="deleteTerm"
                            class="flex-1 h-11 rounded-lg bg-red-600 text-white font-semibold text-sm hover:bg-red-700 transition">
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        // Sliding filter — same logic as Events/Resources pages
        function alignFilterSlider() {
            const slider = document.getElementById('filter-slider');
            if (!slider) return;

            const activeBtn = document.querySelector('#filter-container button.text-gray-900');
            if (activeBtn) {
                slider.style.left = activeBtn.offsetLeft + 'px';
                slider.style.width = activeBtn.offsetWidth + 'px';
            }
        }

        // Run on initial load
        alignFilterSlider();

        // Re-align on window resize
        window.addEventListener('resize', alignFilterSlider);

        // Instantly move slider on click, BEFORE Livewire finishes loading
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

        // Re-align after Livewire navigates (wire:navigate swaps content)
        document.addEventListener('livewire:navigated', alignFilterSlider);
    </script>
</div>