<?php
use Livewire\Volt\Component;
use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin-layout')] class extends Component {
    public function delete($id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Subscriber removed.');
    }

    public function with(): array
    {
        return [
            'subscribers' => NewsletterSubscriber::latest()->paginate(20),
        ];
    }
};
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Newsletter Subscribers</h2>
            <p class="text-sm text-gray-500 mt-1">View and manage public email subscriptions.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.newsletters.subscribers.import') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Import CSV
            </a>
            <a href="{{ route('admin.newsletters.subscribers.export') }}" class="inline-flex items-center px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export CSV
            </a>
            <a href="{{ route('admin.newsletters') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back
            </a>
        </div>
    </div>

    <!-- Subscribers Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($subscribers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Subscribed On</th>
                        <th class="text-right px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subscribers as $subscriber)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $subscriber->email }}
                        </td>
                        <td class="px-6 py-4">
                            @if($subscriber->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                    Pending Verification
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $subscriber->created_at->format('j M Y, g:i a') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="delete({{ $subscriber->id }})" wire:confirm="Are you sure you want to remove this subscriber?" class="p-2 text-gray-400 hover:text-red-500 transition" title="Remove Subscriber">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $subscribers->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="font-semibold text-gray-900">No subscribers yet</h3>
            <p class="text-sm text-gray-500 mt-1">When people subscribe via the public form, they will appear here.</p>
        </div>
        @endif
    </div>
</div>