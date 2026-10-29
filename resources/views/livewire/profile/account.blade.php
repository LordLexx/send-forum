<?php
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.forum-layout')] class extends Component {
    //
};
?>

@php $user = auth()->user(); @endphp

<div>
    <!-- Header -->
    <header class="pb-8 bg-white border-b border-gray-100 -mt-24 pt-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-8">

                <!-- Dynamic Avatar -->
                <div class="shrink-0">
                    @if ($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-32 h-32 md:w-36 md:h-36 rounded-full object-cover shadow-sm ring-4 ring-primary-50">
                    @else
                        <div class="w-32 h-32 md:w-36 md:h-36 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-5xl font-bold shadow-sm ring-4 ring-primary-50">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-500 font-medium">{{ '@' . $user->username }}</p>
                    <p class="text-sm text-gray-400 mt-2 flex items-center justify-center md:justify-start">
                        <svg class="h-4 w-4 mr-1.5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Joined {{ $user->created_at->format('F Y') }}
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('profile') }}" class="w-full md:w-auto border-2 border-gray-200 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-50 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid gridcols-1 lg:grid-cols-12 gap-8">
            <!-- Sidebar -->
            <aside class="hidden lg:block lg:col-span-3">
                <div class="space-y-6 sticky top-24">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                        <nav class="space-y-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-medium text-sm transition">
                                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Profile Details
                            </a>
                            <a href="{{ route('profile.account') }}" class="flex items-center px-3 py-2.5 rounded-lg bg-primary-50 text-primary-700 font-medium text-sm">
                                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                Account & Password
                            </a>
                        </nav>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-6">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <h3 class="font-bold text-orange-800 text-sm">Security Check</h3>
                        </div>
                        <p class="text-orange-700 text-xs leading-relaxed">We recommend using a unique password that you don't use for any other online accounts.</p>
                    </div>
                </div>
            </aside>

            <!-- Main Form Content -->
            <main class="lg:col-span-9 space-y-6">
                <livewire:profile.update-email-form />
                <livewire:profile.update-password-form />
                <livewire:profile.delete-user-form />
            </main>
        </div>
    </div>
</div>