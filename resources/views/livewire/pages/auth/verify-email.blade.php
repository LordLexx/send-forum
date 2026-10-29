<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public ?int $userId = null;
    public string $code = '';
    public string $email = '';

    public function mount($id = null): void
    {
        $this->userId = $id;
        $user = User::find($id);

        if (!$user) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

        // If already verified, send them straight to pending
        if ($user->status !== 'unverified') {
            $this->redirect(route('pending'), navigate: true);
            return;
        }

        $this->email = $user->email;
    }

    public function verify(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::find($this->userId);
        if (!$user) {
            $this->addError('code', 'Account not found.');
            return;
        }

        if (!$user->email_verification_code || $user->email_verification_code !== $this->code) {
            $this->addError('code', 'The verification code is invalid.');
            return;
        }

        if ($user->email_code_expires_at && $user->email_code_expires_at->isPast()) {
            $this->addError('code', 'This code has expired. Please request a new one.');
            return;
        }

        // Mark email verified, move to pending status
        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->email_code_expires_at  = null;
        $user->status = 'pending';
        $user->save();

        // NOW send the pending confirmation email
        Mail::send('emails.pending', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Application Received - Pending Review');
        });

        $this->redirect(route('pending'), navigate: true);
    }

    public function resend(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            $this->addError('code', 'Account not found.');
            return;
        }

        if ($user->status !== 'unverified') {
            $this->redirect(route('pending'), navigate: true);
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->email_code_expires_at  = now()->addMinutes(30);
        $user->save();

        Mail::send('emails.verify-code', ['user' => $user, 'code' => $code], function ($message) use ($user) {
            $message->to($user->email)->subject('Your Email Verification Code');
        });

        $this->dispatch('notify', type: 'success', message: 'A new verification code has been sent to your email.');
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <div class="w-20 h-20 mx-auto rounded-full bg-primary-50 flex items-center justify-center mb-4">
            <svg class="h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Verify Your Email</h1>
        <p class="text-gray-500">We've sent a 6-digit verification code to</p>
        <p class="font-semibold text-gray-700">{{ $email }}</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            </div>
        </div>
    @endif

    <form class="space-y-6" wire:submit="verify">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
            <input
                type="text"
                wire:model="code"
                id="code"
                required
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]{6}"
                placeholder="••••••"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition text-center tracking-[0.5em] font-bold text-lg @error('code') border-red-500 @enderror"
            >
            <p class="text-xs text-gray-500 mt-2 text-center">Enter the 6-digit code from your email.</p>
        </div>

        <div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove wire:target="verify" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg wire:loading wire:target="verify" class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="verify">Verify Email</span>
                <span wire:loading wire:target="verify">Verifying...</span>
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-500">Didn't receive a code?</p>
            <button type="button" wire:click="resend" wire:loading.attr="disabled"
                class="mt-1 text-sm font-medium text-primary-600 hover:text-primary-500 disabled:opacity-50">
                Resend code
            </button>
        </div>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Wrong email? <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary-600 hover:text-primary-500">Start over</a>.
    </p>
</div>
