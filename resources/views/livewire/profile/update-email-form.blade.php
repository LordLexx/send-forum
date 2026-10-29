<?php
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $email = '';

    public function mount(): void {
        $this->email = Auth::user()->email;
    }

    public function updateEmail(): void {
        $user = Auth::user();

        $validated = $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Session::flash('success', 'Email updated successfully.');
        $this->redirect(route('profile.account'));
    }

    public function sendVerification(): void {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('feed', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<form wire:submit="updateEmail" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
    <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Account Email</h2>
    <p class="text-sm text-gray-500 mb-6 ml-11">This is the email address you use to log in. We will also send forum notifications here.</p>

    <div class="max-w-md">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <input type="email" id="email" wire:model="email" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        @if (auth()->user()->hasVerifiedEmail())
            <p class="text-xs text-green-600 mt-2 flex items-center"><svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Verified</p>
        @else
            <p class="text-xs text-orange-600 mt-2">Unverified. <button wire:click="sendVerification" class="underline">Resend link</button></p>
        @endif
    </div>

    <div class="pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-primary-600 rounded-lg font-medium hover:bg-primary-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Update Email
        </button>
    </div>
</form>