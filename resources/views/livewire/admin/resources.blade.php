<?php
use App\Models\Resource;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';      // all, pdfs, links, drafts
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search'        => ['except' => ''],
        'filter'        => ['except' => 'all'],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id): void
    {
        $resource = Resource::find($id);
        if (!$resource) return;

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();
        $this->dispatch('notify', type: 'success', message: 'Resource deleted.');
    }

    public function publish($id): void
    {
        Resource::where('id', $id)->update(['status' => 'published']);
        $this->dispatch('notify', type: 'success', message: 'Resource published.');
    }

    public function archive($id): void
    {
        Resource::where('id', $id)->update(['status' => 'archived']);
        $this->dispatch('notify', type: 'success', message: 'Resource archived.');
    }

    public function unarchive($id): void
    {
        Resource::where('id', $id)->update(['status' => 'published']);
        $this->dispatch('notify', type: 'success', message: 'Resource restored.');
    }

    public function with(): array
    {
        $query = Resource::query()->with('user');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        switch ($this->filter) {
            case 'pdfs':
                $query->where('type', 'file')->where('file_type', 'pdf');
                break;
            case 'links':
                $query->where('type', 'link');
                break;
            case 'drafts':
                $query->where('status', 'draft');
                break;
        }

        // Single ORDER BY — respects the user's chosen sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return ['resources' => $query->paginate(10)];
    }
}; ?>

@section('page_title', 'Manage Resources')

<div class="space-y-6">

    <!-- Controls & Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search -->
        <div class="relative w-full md:max-w-xs">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search resources..." class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <!-- Filters & Actions -->
        <div class="flex items-center space-x-3 w-full md:w-auto justify-between md:justify-end">
            <!-- Sliding filter (mirrors Members page) -->
            <div id="filter-container" class="flex space-x-1 bg-gray-50 p-1 rounded-full border border-gray-100 relative">
                <div id="filter-slider" wire:ignore class="absolute top-1 bottom-1 rounded-full bg-white shadow-sm transition-all duration-300 ease-out"></div>
                <button data-filter="all" wire:click="$set('filter', 'all')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'all' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                <button data-filter="pdfs" wire:click="$set('filter', 'pdfs')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'pdfs' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">PDFs</button>
                <button data-filter="links" wire:click="$set('filter', 'links')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'links' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Links</button>
                <button data-filter="drafts" wire:click="$set('filter', 'drafts')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $filter === 'drafts' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Drafts</button>
            </div>

            <a href="{{ route('admin.resources.create') }}" wire:navigate class="flex items-center bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Resource
            </a>
        </div>
    </div>

    <!-- Resources Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Resource Title (Sortable) -->
                        <th scope="col" wire:click="sortBy('title')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Resource Title</span>
                                @if($sortField === 'title' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @elseif($sortField === 'title' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                @endif
                            </div>
                        </th>
                        <!-- Type (Sortable) -->
                        <th scope="col" wire:click="sortBy('type')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Type</span>
                                @if($sortField === 'type' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @elseif($sortField === 'type' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                @endif
                            </div>
                        </th>
                        <!-- Status (Sortable) -->
                        <th scope="col" wire:click="sortBy('status')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Status</span>
                                @if($sortField === 'status' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @elseif($sortField === 'status' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                @endif
                            </div>
                        </th>
                        <!-- Uploaded (Sortable) -->
                        <th scope="col" wire:click="sortBy('created_at')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <span>Uploaded</span>
                                @if($sortField === 'created_at' && $sortDirection === 'asc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @elseif($sortField === 'created_at' && $sortDirection === 'desc')
                                    <svg class="w-4 h-4 ml-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @else
                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse ($resources as $resource)
                        @php
                            $isArchived = $resource->status === 'archived';
                            $isDraft    = $resource->status === 'draft';
                            $isLink     = $resource->type === 'link';
                            $isPdf      = $resource->file_type === 'pdf';
                            $isWord     = in_array($resource->file_type, ['doc', 'docx']);
                            $isImage    = in_array($resource->file_type, ['png', 'jpg', 'jpeg']);
                        @endphp

                        <tr wire:key="resource-{{ $resource->id }}" class="hover:bg-gray-50 transition {{ $isDraft ? 'bg-yellow-50/30' : '' }} {{ $isArchived ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    {{-- Icon --}}
                                    @if ($isArchived)
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </div>
                                    @elseif ($isLink)
                                        <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center text-primary-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                        </div>
                                    @elseif ($isPdf)
                                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </div>
                                    @elseif ($isWord)
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                    @elseif ($isImage)
                                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 mr-4">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900">{{ $resource->title }}</div>
                                        @if ($resource->original_filename)
                                            <div class="text-xs text-gray-400 truncate max-w-xs">{{ $resource->original_filename }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($isLink)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-700">Link</span>
                                @elseif ($isPdf)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">PDF</span>
                                @elseif ($isWord)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-700">Word Doc</span>
                                @elseif ($isImage)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-700">Image</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">File</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($resource->status === 'published')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">Published</span>
                                @elseif ($resource->status === 'draft')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-700">Draft</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">Archived</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resource->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">

                                    <a href="{{ route('admin.resources.edit', $resource->id) }}" wire:navigate class="inline-flex items-center px-2.5 py-1.5 border border-gray-200 text-gray-700 bg-white rounded-md text-xs font-medium hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Edit
                                    </a>

                                    @if ($resource->status === 'draft')
                                        <button wire:click="publish({{ $resource->id }})" wire:confirm="Publish this resource now?" class="inline-flex items-center px-2.5 py-1.5 border border-green-200 text-green-700 bg-green-50 rounded-md text-xs font-medium hover:bg-green-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            Publish
                                        </button>
                                    @elseif ($resource->status === 'published')
                                        <button wire:click="archive({{ $resource->id }})" wire:confirm="Archive this resource? It will be hidden from members." class="inline-flex items-center px-2.5 py-1.5 border border-orange-200 text-orange-700 bg-orange-50 rounded-md text-xs font-medium hover:bg-orange-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                            Archive
                                        </button>
                                    @elseif ($resource->status === 'archived')
                                        <button wire:click="unarchive({{ $resource->id }})" wire:confirm="Restore this resource?" class="inline-flex items-center px-2.5 py-1.5 border border-primary-200 text-primary-700 bg-primary-50 rounded-md text-xs font-medium hover:bg-primary-100 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            Restore
                                        </button>
                                    @endif

                                    <button wire:click="delete({{ $resource->id }})" wire:confirm="Are you sure you want to permanently delete this resource? This cannot be undone." class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                <p class="font-medium">No resources found.</p>
                                <p class="text-xs mt-1">Try adjusting your search or filter, or create a new resource.</p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="hidden sm:block">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-medium text-gray-900">{{ $resources->firstItem() ?? 0 }}</span> to <span class="font-medium text-gray-900">{{ $resources->lastItem() ?? 0 }}</span> of <span class="font-medium text-gray-900">{{ $resources->total() }}</span> resources
                </p>
            </div>
            <div class="flex-1 flex justify-center sm:justify-end">
                {{ $resources->links() }}
            </div>
        </div>
    </div>

    <script>
        // Sliding filter — same logic as Members page
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