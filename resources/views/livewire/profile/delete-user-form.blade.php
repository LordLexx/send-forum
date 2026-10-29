<?php
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    public function deleteUser(Logout $logout): void {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);
        tap(Auth::user(), $logout(...))->delete();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="bg-white rounded-2xl shadow-sm border-2 border-red-100 p-6 md:p-8 space-y-6">
    <h2 class="text-xl font-bold text-red-700 mb-1 flex items-center"><span class="w-8 h-1 bg-red-500 rounded-full mr-3"></span>Danger Zone</h2>
    <p class="text-sm text-gray-500 mb-6 ml-11">Permanently delete your account and all associated data. This action cannot be undone.</p>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-red-50/50 border border-red-100 rounded-xl p-4">
        <div>
            <p class="font-semibold text-gray-900 text-sm">Delete Account</p>
            <p class="text-xs text-gray-500 mt-1">All your posts, replies, and profile data will be erased forever.</p>
        </div>
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="shrink-0 w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-white bg-red-600 rounded-lg font-medium hover:bg-red-700 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            Delete Account
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Are you sure you want to delete your account?') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input wire:model="password" id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-danger-button class="ms-3">{{ __('Delete Account') }}</x-danger-button>
            </div>
        </form>
    </x-modal>
</div>