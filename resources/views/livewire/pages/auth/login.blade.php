<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        // 1. Check credentials WITHOUT logging in.
        // This catches BOTH cases:
        //    - Email doesn't exist in the database at all
        //    - Email exists, but wrong password
        // Either way → red error message, no redirect anywhere.
        if (!Auth::validate(['email' => $this->form->email, 'password' => $this->form->password])) {
            throw ValidationException::withMessages([
                'form.email' => __('The provided credentials do not match our records.'),
            ]);
        }

        // 2. Get the user to check their application status
        $user = User::where('email', $this->form->email)->first();

        // 3. CHANGED: If they haven't verified their email, throw a red error
        //    message — do NOT redirect to the verify-email page.
        //    The verify-email page is ONLY for fresh registrations.
        if ($user->status === 'unverified') {
            throw ValidationException::withMessages([
                'form.email' => 'Your email address has not been verified yet. Please check your inbox for the 6-digit code, or register again to receive a new one.',
            ]);
        }

        // 4. Redirect based on status (these are fine — they go to dedicated
        //    status pages, not the verify-email page)
        if ($user->status === 'pending') {
            Mail::send('emails.pending', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Login Attempt - Account Pending');
            });

            $this->redirect(route('still.pending'), navigate: true);
            return;
        }

        if ($user->status === 'banned') {
            Mail::send('emails.suspended', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Login Attempt - Account Suspended');
            });

            $this->redirect(route('suspended'), navigate: true);
            return;
        }

        if ($user->status === 'rejected') {
            Mail::send('emails.rejected', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Login Attempt - Application Rejected');
            });

            $this->redirect(route('rejected'), navigate: true);
            return;
        }

        // 5. If approved, log them in normally
        Auth::login($user, $this->form->remember);
        Session::regenerate();

        $this->redirectIntended(default: route('feed', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Form Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Log in to your account</h1>
        <p class="text-gray-500">Enter your details to access the forum.</p>
    </div>

    <!-- Validation Error Alert Box -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Login Form -->
    <form class="space-y-6" wire:submit="login">
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input type="email" wire:model="form.email" id="email" name="email" required autofocus
                    autocomplete="username"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('form.email') border-red-500 @enderror"
                    placeholder="you@example.com">
            </div>
        </div>

        <!-- Password Input -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="text-sm font-medium text-primary-600 hover:text-primary-500 transition">Forgot password?</a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input type="password" wire:model="form.password" id="password" name="password" required
                    autocomplete="current-password"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('form.password') border-red-500 @enderror"
                    placeholder="••••••••">
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input wire:model="form.remember" id="remember-me" name="remember-me" type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Keep me logged in on this device</label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove wire:target="login" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <svg wire:loading wire:target="login" class="animate-spin h-4 w-4 mr-2"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="login">Log In to Forum</span>
                <span wire:loading wire:target="login">Logging in...</span>
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div class="mt-8">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-400">Not a member yet?</span>
            </div>
        </div>
    </div>

    <!-- Register Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-4">If you are a parent carer of a child with SEND in Slough, you can request
            access to our private community.</p>
        <a href="{{ route('register') }}" wire:navigate
            class="inline-block w-full justify-center py-3 px-4 border-2 border-primary-600 rounded-lg shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 transition duration-300">
            Request Access to Join
        </a>
    </div>
</div>
