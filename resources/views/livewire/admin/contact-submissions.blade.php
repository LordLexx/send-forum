<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\ContactSubmission;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public string $search       = '';
    public string $statusFilter = 'all';
    public string $topicFilter  = 'all';
    public string $sortBy       = 'created_at';
    public string $sortDir      = 'desc';

    public function updating($property): void
    {
        if (in_array($property, ['search', 'statusFilter', 'topicFilter'])) {
            $this->resetPage();
        }
    }

    public function toggleSort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function with(): array
    {
        $query = ContactSubmission::where('is_verified', true);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name',    'like', "%{$this->search}%")
                  ->orWhere('email',  'like', "%{$this->search}%")
                  ->orWhere('subject','like', "%{$this->search}%")
                  ->orWhere('message','like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->topicFilter !== 'all') {
            $query->where('topic', $this->topicFilter);
        }

        $query->orderBy($this->sortBy, $this->sortDir);

        return [
            'submissions' => $query->paginate(15),
            'newCount'    => ContactSubmission::where('is_verified', true)->where('status', ContactSubmission::STATUS_NEW)->count(),
            'totalCount'  => ContactSubmission::where('is_verified', true)->count(),
        ];
    }
};
?>

<div>
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contact Submissions</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $totalCount }} total ·
                <span class="text-red-600 font-medium">{{ $newCount }} new</span>
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name, email, subject…"
                       class="w-full rounded-lg border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                        class="w-full rounded-lg border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none bg-white">
                    <option value="all">All Statuses</option>
                    <option value="new">New</option>
                    <option value="read">Read</option>
                    <option value="responded">Responded</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Topic</label>
                <select wire:model.live="topicFilter"
                        class="w-full rounded-lg border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none bg-white">
                    <option value="all">All Topics</option>
                    <option value="general">General Enquiry</option>
                    <option value="event">Event Booking</option>
                    <option value="steering">Joining Steering Committee</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($submissions->isEmpty())
            <div class="text-center py-16">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center text-2xl mb-3">📭</div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">No submissions found</h3>
                <p class="text-sm text-gray-500">Try adjusting your filters or search terms.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">
                                <button wire:click="toggleSort('created_at')" class="flex items-center gap-1 hover:text-gray-900">
                                    Date
                                    @if ($sortBy === 'created_at')
                                        <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">
                                <button wire:click="toggleSort('name')" class="flex items-center gap-1 hover:text-gray-900">
                                    Name
                                    @if ($sortBy === 'name')
                                        <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">Topic</th>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">Subject</th>
                            <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">Status</th>
                            <th class="text-right text-xs font-semibold text-gray-600 uppercase tracking-wide px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($submissions as $submission)
                            <tr class="hover:bg-gray-50 transition @if ($submission->status === 'new') bg-red-50/30 @endif">
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $submission->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $submission->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $submission->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $submission->topic_label }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ $submission->subject }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }}">
                                        {{ $submission->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.contact-submissions.show', $submission) }}" wire:navigate
                                       class="inline-flex items-center justify-center text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        View
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
