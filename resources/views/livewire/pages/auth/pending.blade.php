<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Simple static page component
}; ?>

<div class="flex-grow flex items-center justify-center pt-20 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full">

        <!-- Pending Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-10 text-center">

            <!-- Icon -->
            <div class="w-20 h-20 mx-auto rounded-full bg-primary-50 flex items-center justify-center mb-6">
                <svg class="h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Application Submitted</h1>

            <!-- Body Copy -->
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                Thank you for registering. Your application request has been successfully submitted and is now pending
                review by our administrative team.
            </p>

            <!-- Info Box -->
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 mb-8 text-left">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>To ensure our community remains a safe, secure, and supportive space for all parent carers,
                            all new accounts are manually reviewed before access is granted.</p>
                        <p class="font-medium text-gray-800">You will receive an email notification once your account
                            has been approved. This process typically takes up to 48 hours.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('welcome') }}" wire:navigate
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-full font-medium hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Return to Homepage
                </a>
                <a href="{{ route('login') }}" wire:navigate
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white gradient-bg rounded-full font-medium hover:opacity-90 transition">
                    Back to Login
                </a>
            </div>
        </div>

        <!-- Footnote -->
        <p class="text-center text-xs text-gray-400 mt-6">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</div>