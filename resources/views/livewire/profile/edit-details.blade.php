<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $username = '';
    public string $phone = '';
    public string $city = '';
    public string $address = '';
    public string $postcode = '';
    public string $signature = '';
    public string $photo_permission = 'no';

    // Dynamic arrays for children
    public array $children = [];

    // Photo upload (temporary until saved)
    public $photo;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->phone = $user->phone ?? '';
        $this->city = $user->city ?? '';
        $this->address = $user->address ?? '';
        $this->postcode = $user->postcode ?? '';
        $this->signature = $user->signature ?? '';
        $this->photo_permission = $user->photo_permission ?? 'no';

        // Load children (fallback to old fields if empty)
        $this->children = $user->children
            ? (is_array($user->children) ? $user->children : json_decode($user->children, true))
            : [['name' => $user->child_name ?? '', 'age' => $user->child_age ?? '']];
    }

    public function addPerson(): void
    {
        // Prevent adding if the limit of 5 is reached
        if (count($this->children) >= 5) {
            return;
        }

        // Prevent adding if there are existing validation errors
        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        // Prevent adding if the last child has empty name and age
        $lastChild = end($this->children);
        if ($lastChild && empty($lastChild['name']) && empty($lastChild['age'])) {
            return;
        }

        $this->children[] = ['name' => '', 'age' => ''];
    }

    public function removePerson($index): void
    {
        $this->resetErrorBag(); // Clear previous validation errors
        unset($this->children[$index]);
        $this->children = array_values($this->children);
    }

    // Only validate the photo on upload, don't save it yet!
    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB Max
        ]);
    }

    public function updateDetails(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'address' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:250'],
            'photo_permission' => ['required', 'in:yes,no'],
            'children' => ['nullable', 'array'],
            'children.*.name' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'], // Optional, but must be letters
            'children.*.age' => ['nullable', 'integer', 'min:0', 'max:25'], // Optional, but must be 25 or under
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.regex' => 'The name may only contain letters, spaces, and hyphens.',
            'city.regex' => 'The city may only contain letters, spaces, and hyphens.',
            'username.alpha_dash' => 'The username may only contain letters, numbers, dashes, and underscores.',
            'children.*.name.regex' => 'The child name may only contain letters, spaces, and hyphens.',
        ]);

        // Process Photo ONLY if a new one was uploaded
        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');

            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $path;
        }

        // Remove the temporary photo object from the array before saving
        unset($validated['photo']);

        // Clean up empty children entries
        $validated['children'] = array_filter($validated['children'], fn($child) => !empty($child['name']) || !empty($child['age']));

        $user->update($validated);

        // Flash success to session (Standard Laravel way)
        Session::flash('success', 'Profile updated successfully.');

        // Redirect to the profile show page (Standard HTTP redirect)
        $this->redirect(route('profile'));
    }
}; ?>

<!--
    Alpine x-data watches for any input changes.
    If changed = true, the browser will warn the user before they close the tab or navigate away.
-->
<form
    wire:submit="updateDetails"
    x-data="{ changed: false }"
    @change="changed = true"
    @input="changed = true"
    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-8"
>

    <!-- Unsaved Changes Banner -->
    <div x-show="changed" x-cloak class="p-3 bg-yellow-50 text-yellow-700 rounded-lg text-sm flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        You have unsaved changes. Remember to click Save!
    </div>

    <!-- Basic Info -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Basic Information</h2>
        <p class="text-sm text-gray-500 mb-6 ml-11">Update your personal details and how you appear to the community.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Avatar Upload -->
            <div class="md:col-span-2 flex flex-col items-center mb-4">
                <label for="photo" class="cursor-pointer block relative">

                    @if ($photo)
                        <!-- Live Temporary Preview -->
                        <img src="{{ $photo->temporaryUrl() }}" class="w-28 h-28 rounded-full object-cover shadow-sm ring-4 ring-primary-50">
                    @elseif (auth()->user()->profile_photo_path)
                        <!-- Saved Photo -->
                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-28 h-28 rounded-full object-cover shadow-sm ring-4 ring-primary-50">
                    @else
                        <!-- Initials Fallback -->
                        <div class="w-28 h-28 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-4xl font-bold shadow-sm ring-4 ring-primary-50">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <span class="absolute bottom-1 right-1 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 border border-gray-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </span>

                    <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center text-white text-sm">
                        Uploading...
                    </div>
                </label>
                <input id="photo" type="file" class="hidden" wire:model="photo">
                @error('photo') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" wire:model="name" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Editable Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-2.5 rounded-l-lg border border-r-0 @error('username') border-red-500 @else border-gray-200 @endif bg-gray-100 text-gray-500 text-sm">@</span>
                    <input type="text" id="username" wire:model="username" class="w-full px-4 py-2.5 rounded-r-lg bg-gray-50 border @error('username') border-red-500 @else border-gray-200 @enderror focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                </div>
                @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Contact Details -->
    <div class="pt-6 border-t border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Contact Details</h2>
        <p class="text-sm text-gray-500 mb-6 ml-11">Used by admins for verification purposes. Kept completely private.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telephone Number <span class="text-red-500">*</span></label>
                <input type="tel" id="phone" wire:model="phone" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Town/City <span class="text-red-500">*</span></label>
                <input type="text" id="city" wire:model="city" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                <input type="text" id="address" wire:model="address" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Postcode <span class="text-red-500">*</span></label>
                <input type="text" id="postcode" wire:model="postcode" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                @error('postcode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Family Details -->
    <div class="pt-6 border-t border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Family Details</h2>
        <p class="text-sm text-gray-500 mb-6 ml-11">Information about your child or young person. (Completely optional).</p>

        <div class="space-y-4">
            @foreach($children as $index => $child)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center relative">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Child/Young Person's Name</label>
                        <input type="text" wire:model="children.{{ $index }}.name" placeholder="e.g. Leo" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        @error("children.{$index}.name") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Age (Optional)</label>
                            <input type="number" wire:model="children.{{ $index }}.age" placeholder="e.g. 7" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                            @error("children.{$index}.age") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        @if($index > 0)
                            <button type="button" wire:click="removePerson({{ $index }})" class="mb-1 p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" aria-label="Remove">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Smaller Add Button (Hidden if limit reached or fields empty) -->
        <div class="mt-4" x-data="{ limit: 5 }">
            <button
                type="button"
                wire:click="addPerson"
                x-show="$wire.children.length < limit"
                :disabled="$wire.children.length > 0 && $wire.children[$wire.children.length - 1].name === '' && $wire.children[$wire.children.length - 1].age === ''"
                class="inline-flex items-center justify-center px-3 py-1.5 text-xs border border-dashed border-primary-300 text-primary-600 bg-primary-50 rounded-full font-medium hover:bg-primary-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add another person
            </button>

            <!-- Limit Message -->
            <p x-show="$wire.children.length >= limit" x-cloak class="text-xs text-gray-400 mt-2">
                You've reached the maximum limit of 5 family members.
            </p>
        </div>
    </div>

    <!-- About Me -->
    <div class="pt-6 border-t border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>About Me</h2>
        <p class="text-sm text-gray-500 mb-6 ml-11">Share a bit about yourself. This will be visible to other members.</p>
        <div>
            <label for="signature" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea id="signature" wire:model="signature" rows="4" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm resize-none" placeholder="Tell the community about yourself..."></textarea>
            <p class="text-xs text-gray-400 mt-1">Max 250 characters.</p>
        </div>
    </div>

    <!-- Privacy -->
    <div class="pt-6 border-t border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center"><span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Privacy & Permissions</h2>
        <p class="text-sm text-gray-500 mb-6 ml-11">Manage your consent for forum activities.</p>
        <div>
            <label for="photo_permission" class="block text-sm font-medium text-gray-700 mb-1">Photo Permission</label>
            <div class="relative">
                <select id="photo_permission" wire:model="photo_permission" class="w-full sm:w-1/2 h-11 pl-4 pr-10 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm appearance-none">
                    <option value="no">I do NOT give permission for photos to be used</option>
                    <option value="yes">I give permission for photos to be used</option>
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
            <p class="text-xs text-gray-400 mt-1">Do you allow photos of you/your child to be used in Slough SEND PCF publications or social media?</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('profile') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition">Cancel</a>
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-primary-600 rounded-lg font-medium hover:bg-primary-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Save Changes
        </button>
    </div>
</form>