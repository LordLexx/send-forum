<div class="relative inline-flex shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button @click="open = !open; if(open && {{ $unreadCount }} > 0) { $wire.markAsRead() }" class="relative p-1.5 min-[420px]:p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-full transition" aria-label="Notifications">
        <svg class="h-5 w-5 min-[420px]:h-6 min-[420px]:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-orange-500 rounded-full border-2 border-white animate-pulse"></span>
        @endif
    </button>

    <div x-show="open" x-cloak
         class="absolute right-0 mt-2 w-80 sm:w-96 max-w-[calc(100vw-4rem)] bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95">

        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900 text-sm">Notifications</h3>
            @if($unreadCount > 0)
                <span class="text-xs font-medium text-white bg-orange-500 px-2 py-0.5 rounded-full">{{ $unreadCount }} New</span>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <!-- Change the notification div to an <a> tag -->
                <a href="{{ route('notifications.read', $notification->id) }}" class="flex items-start px-4 py-3 hover:bg-gray-50 transition {{ $isUnread ? 'bg-orange-50/40' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-primary-50 flex items-center justify-center text-primary-600 mr-3 shrink-0">
                        @if(str_contains($notification->type, 'NewHug'))
                            <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        @elseif(str_contains($notification->type, 'NewComment'))
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        @elseif(str_contains($notification->type, 'NewEvent'))
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        @else
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700 {{ $isUnread ? 'font-semibold' : 'font-normal' }}">{{ $data['message'] ?? 'New notification' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if($isUnread)
                        <span class="w-2 h-2 bg-orange-500 rounded-full mt-2 shrink-0"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <p class="text-sm text-gray-500">No notifications yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>