<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Simple static page component
}; ?>

<div class="flex-grow flex items-center justify-center pt-20 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full">

        <!-- Suspended Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-10 text-center">

            <!-- Icon (Shield/Ban) -->
            <div class="w-20 h-20 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-6">
                <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Account Suspended</h1>

            <!-- Body Copy -->
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                Your account has been suspended due to a violation of our community guidelines or Terms of Service.
            </p>

            <!-- Info Box -->
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 mb-8 text-left">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>While we strive to maintain a supportive environment for all parent carers, we must enforce our guidelines to ensure the safety and comfort of everyone in the forum.</p>
                        <p class="font-medium text-gray-800">If you believe this suspension was made in error, or if you wish to submit an appeal, please contact our administrative team.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('welcome') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-full font-medium hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Return to Homepage
                </a>
                <a href="mailto:admin@sloughsend.org.uk" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-red-500 rounded-full font-medium hover:bg-red-600 transition">
                    Contact Support
                </a>
            </div>
        </div>

        <!-- Footnote -->
        <p class="text-center text-xs text-gray-400 mt-6">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</div>