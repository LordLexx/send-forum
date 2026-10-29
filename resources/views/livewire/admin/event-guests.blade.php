<?php
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public Event $event;
    public string $search = '';

    public function mount(Event $event) {
        $this->event = $event;
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function with(): array {
        $registrations = $this->event->registrations()
            ->when($this->search, function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return compact('registrations');
    }
};
?>

@section('page_title', 'Event Guests')

<div class="space-y-6">

    <!-- Header / Breadcrumb -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.events') }}" wire:navigate class="text-sm text-gray-500 hover:text-primary-600 mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Events
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Guest List</h2>
            <p class="text-sm text-gray-500 mt-1">Registered emails for: <span class="font-semibold text-gray-700">{{ $event->title }}</span></p>
        </div>

        <!-- Download Button -->
        <a href="{{ route('admin.events.guests.download', $event) }}"
           class="flex items-center bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition shadow-sm">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download CSV
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:max-w-xs">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search emails..." class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <div class="text-sm text-gray-500">
            Total Registered: <span class="font-bold text-gray-900">{{ $event->registrations->count() }}</span>
        </div>
    </div>

    <!-- Guests Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Registered At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($registrations as $index => $registration)
                        <tr wire:key="reg-{{ $registration->id }}" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $registrations->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $registration->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $registration->created_at->format('M d, Y - g:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">No registrations yet</p>
                                <p class="mt-1 text-sm text-gray-500">When users register, their emails will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($registrations->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <div class="hidden sm:block">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-900">{{ $registrations->firstItem() ?? 0 }}</span> to <span class="font-medium text-gray-900">{{ $registrations->lastItem() ?? 0 }}</span> of <span class="font-medium text-gray-900">{{ $registrations->total() }}</span> guests
                    </p>
                </div>
                <div class="flex-1 flex justify-center sm:justify-end">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{ $registrations->links() }}
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>