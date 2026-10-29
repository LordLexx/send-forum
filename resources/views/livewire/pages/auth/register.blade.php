<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $address = '';
    public string $city = '';
    public string $postcode = '';
    public string $phone = '';

    public ?string $child_name = '';
    public ?string $child_age = '';

    public string $photo_permission = 'no';
    public string $signature = '';
    public string $date = '';
    public bool $terms = false;

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // ⬇️ Removed the 'unique' rule — we'll check manually below
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],

            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],

            'child_name' => ['nullable', 'string', 'max:255'],
            'child_age' => ['nullable', 'string', 'max:255'],

            'photo_permission' => ['required', 'in:yes,no'],
            'signature' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'terms' => ['accepted'],
        ]);

        // --- Check if email is already in use ---
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser && $existingUser->status !== 'unverified') {
            // Account exists and is verified/pending/approved/banned → block re-registration
            throw ValidationException::withMessages([
                'email' => 'An account with this email already exists. Try logging in or resetting your password.',
            ]);
        }
        // ----------------------------------------

        $validated['password'] = Hash::make($validated['password']);
        unset($validated['terms']);

        // --- AUTO-GENERATE UNIQUE USERNAME (exclude existing user if updating) ---
        $baseUsername = Str::slug($validated['name'], '_');
        $username = $baseUsername;
        $count = 1;

        while (true) {
            $query = User::where('username', $username);
            if ($existingUser) {
                $query->where('id', '!=', $existingUser->id);
            }
            if (! $query->exists()) {
                break;
            }
            $username = $baseUsername . '_' . $count;
            $count++;
        }
        $validated['username'] = $username;
        // ---------------------------------------------------------------------

        // --- GENERATE FRESH 6-DIGIT VERIFICATION CODE ---
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $validated['email_verification_code'] = $code;
        $validated['email_code_expires_at']  = now()->addMinutes(30);
        $validated['status'] = 'unverified';
        // ------------------------------------------------

        // --- CREATE OR UPDATE ---
        if ($existingUser) {
            // They're re-registering after failing verification — update existing record
            $existingUser->fill($validated);
            $existingUser->save();
            $user = $existingUser;
        } else {
            // Brand new registration
            $user = User::create($validated);
        }
        // ------------------------

        event(new Registered($user));

        // --- SEND VERIFICATION CODE EMAIL ---
        Mail::send('emails.verify-code', ['user' => $user, 'code' => $code], function ($message) use ($user) {
            $message->to($user->email)->subject('Your Email Verification Code');
        });
        // -------------------------------------

        // Redirect to the verify-email page
        $this->redirect(route('verify.email', ['id' => $user->id]), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Become a Member</h1>
        <p class="text-gray-500">Fill in the form below to apply to become a member.</p>
    </div>

    <div class="bg-primary-50 border-l-4 border-primary-500 p-4 rounded-r-lg mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-primary-800"><strong>You do not need a formal diagnosis to join.</strong> If you
                    believe your child has additional needs, your voice and experience are important.</p>
            </div>
        </div>
    </div>

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

    <form class="space-y-6" wire:submit="register">

        <div class="space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Your Details</h3>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" wire:model="name" id="name" required
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                    placeholder="Jane Doe">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="email" id="email" required
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                    placeholder="you@example.com">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" wire:model="password" id="password" required
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('password') border-red-500 @enderror">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm
                        Password</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation" required
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('password_confirmation') border-red-500 @enderror">
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" wire:model="address" id="address" required
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('address') border-red-500 @enderror"
                    placeholder="123 Slough Street">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Town/City</label>
                    <input type="text" wire:model="city" id="city" required
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('city') border-red-500 @enderror"
                        placeholder="Slough">
                </div>
                <div>
                    <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input type="text" wire:model="postcode" id="postcode" required
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('postcode') border-red-500 @enderror"
                        placeholder="SL1 0AA">
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
                <input type="tel" wire:model="phone" id="phone" required
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('phone') border-red-500 @enderror"
                    placeholder="07123 456789">
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Child / Young Person Details</h3>

            <div>
                <label for="child_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Child/Young Person
                    <span class="text-gray-400 normal-case font-normal">(optional)</span></label>
                <input type="text" wire:model="child_name" id="child_name"
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="child_age" class="block text-sm font-medium text-gray-700 mb-1">Age of Child/Young
                    Person</label>
                <input type="number" wire:model="child_age" id="child_age" min="0" max="25"
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    placeholder="e.g. 8">
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Photo Permission</h3>
            <p class="text-sm text-gray-500">Do you give permission for photographs of you/my child to be used in Slough
                SEND Parent Carer Forum publications, reports or social media?</p>

            <div class="space-y-3 mt-2">
                <div class="relative flex items-center">
                    <input type="radio" wire:model="photo_permission" id="photo_yes" name="photo_permission" value="yes"
                        class="opacity-0 absolute h-5 w-5 cursor-pointer">
                    <label for="photo_yes"
                        class="radio-card flex-1 block w-full p-3 border-2 border-gray-200 rounded-lg cursor-pointer text-sm text-gray-700">
                        <span class="font-semibold text-primary-700">Yes,</span> I give permission for photographs to be
                        used.
                    </label>
                </div>
                <div class="relative flex items-center">
                    <input type="radio" wire:model="photo_permission" id="photo_no" name="photo_permission" value="no"
                        class="opacity-0 absolute h-5 w-5 cursor-pointer" checked>
                    <label for="photo_no"
                        class="radio-card flex-1 block w-full p-3 border-2 border-gray-200 rounded-lg cursor-pointer text-sm text-gray-700">
                        <span class="font-semibold text-red-600">No,</span> I do NOT give permission for photographs to
                        be used.
                    </label>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Verification & Signature</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="signature" class="block text-sm font-medium text-gray-700 mb-1">Signature <span
                            class="text-gray-400 normal-case font-normal">(Type full name)</span></label>
                    <input type="text" wire:model="signature" id="signature" required
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('signature') border-red-500 @enderror">
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" wire:model="date" id="date" required disabled
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition @error('date') border-red-500 @enderror">
                </div>
            </div>

            <div class="flex items-start mt-4">
                <div class="flex items-center h-5">
                    <input wire:model="terms" id="terms" name="terms" type="checkbox" required
                        class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="font-medium text-gray-700">I agree to the terms of the <a href="#"
                            class="text-primary-600 hover:underline">Privacy Policy</a></label>
                    @error('terms')
                    <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove wire:target="register" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <svg wire:loading wire:target="register" class="animate-spin h-4 w-4 mr-2"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="register">Submit Application</span>
                <span wire:loading wire:target="register">Submitting...</span>
            </button>
            <p class="text-center text-xs text-gray-400 mt-3">Your application will be reviewed by our admin team before
                access is granted.</p>
        </div>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Already a member? <a href="{{ route('login') }}" wire:navigate
            class="font-medium text-primary-600 hover:text-primary-500">Log in here</a>.
    </p>
</div>
