<?php
use Livewire\Volt\Component;
use App\Models\Newsletter;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.admin-layout')] class extends Component {
    public function delete($id)
    {
        Newsletter::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Newsletter deleted.');
    }

    public function with(): array
    {
        return [
            'newsletters' => Newsletter::with('creator')
                ->latest()
                ->paginate(15),
        ];
    }
};
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Newsletters</h2>
            <p class="text-sm text-gray-500 mt-1">Create, manage and send newsletters to your community.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.newsletters.subscribers') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" /></svg>
                View Subscribers
            </a>
            <a href="{{ route('admin.newsletters.create') }}" class="inline-flex items-center px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Newsletter
            </a>
        </div>
    </div>

    <!-- Newsletters Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($newsletters->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Recipients</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="text-right px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($newsletters as $newsletter)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $newsletter->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $newsletter->subject ?: 'No subject line' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($newsletter->status === 'sent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Sent {{ $newsletter->sent_at?->diffForHumans() }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ ucfirst($newsletter->recipient_group) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $newsletter->created_at->format('j M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="p-2 text-gray-400 hover:text-primary-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <button wire:click="delete({{ $newsletter->id }})" wire:confirm="Are you sure you want to delete this newsletter?" class="p-2 text-gray-400 hover:text-red-500 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $newsletters->links() }}
        @else
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="font-semibold text-gray-900">No newsletters yet</h3>
            <p class="text-sm text-gray-500 mt-1">Create your first newsletter to get started.</p>
            <a href="{{ route('admin.newsletters.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                Create Newsletter
            </a>
        </div>
        @endif
    </div>
</div>