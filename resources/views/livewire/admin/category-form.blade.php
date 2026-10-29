<?php
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-layout')] class extends Component {
    public $categoryId = null;
    public $icon = '💬';
    public $name = '';
    public $description = '';
    public $status = 'active';
    public $make_priority = true; // ← NEW PROPERTY

    public function mount($id = null): void
    {
        if ($id) {
            $category = Category::findOrFail($id);
            $this->categoryId = $category->id;
            $this->icon = $category->icon;
            $this->name = $category->name;
            $this->description = $category->description;
            $this->status = $category->status;
            $this->make_priority = (bool) $category->make_priority; // ← NEW
        } else {
            // If creating a new category, check if the limit is already reached
            $currentPriorityCount = Category::where('make_priority', true)->count();
            if ($currentPriorityCount >= 15) {
                $this->make_priority = false; // Force false if limit reached
            }
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'icon' => 'required|string|max:2',
            'name' => 'required|string|max:50',
            'description' => 'required|string',
            'status' => 'required|in:active,locked,hidden',
            'make_priority' => 'boolean', // ← NEW VALIDATION RULE
        ]);

        // --- ENFORCE 15 PRIORITY LIMIT ---
        if ($validated['make_priority']) {
            $count = Category::where('make_priority', true)
                ->when($this->categoryId && $this->make_priority, fn($q) => $q->where('id', '!=', $this->categoryId))
                ->count();

            if ($count >= 15) {
                $this->dispatch('notify', type: 'error', message: 'Limit of 15 priority categories reached. Please disable priority on another category first.');
                $this->addError('make_priority', 'Limit reached. Disable priority on another category first.');
                return;
            }
        }

        // --- AUTO-GENERATE SLUG IN SECRET ---
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->when($this->categoryId, fn($q) => $q->where('id', '!=', $this->categoryId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $validated['slug'] = $slug;

        if ($this->categoryId) {
            $category = Category::find($this->categoryId);
            $category->update($validated);
            $this->dispatch('notify', type: 'success', message: 'Category updated successfully.');
        } else {
            Category::create($validated);
            $this->dispatch('notify', type: 'success', message: 'Category created successfully.');
        }

        $this->redirect(route('admin.categories'), navigate: true);
    }

    public function render(): mixed
    {
        // Calculate current priority count to dynamically disable the checkbox in the UI
        // Exclude this category from the count if it is already a priority category
        $currentPriorityCount = Category::where('make_priority', true)
            ->when($this->categoryId && $this->make_priority, fn($q) => $q->where('id', '!=', $this->categoryId))
            ->count();

        $priorityLimitReached = $currentPriorityCount >= 15 && !$this->make_priority;

        return view('livewire.admin.category-form', [
            'currentPriorityCount' => $currentPriorityCount,
            'priorityLimitReached' => $priorityLimitReached,
        ]);
    }
}; ?>

@section('page_title', $categoryId ? 'Edit Category' : 'Create Category')

<div class="space-y-6">

    <!-- Form Container -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">

                <!-- Icon & Name Group -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Icon Input -->
                    <div class="sm:w-20">
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <div class="relative">
                            <input type="text" id="icon" wire:model.live="icon" maxlength="2" readonly
                                class="w-full h-11 pl-3 pr-8 text-center text-xl rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none cursor-pointer">

                            <!-- Emoji Trigger Button -->
                            <button type="button" id="emoji-picker-btn"
                                class="absolute inset-y-0 right-0 px-2 flex items-center text-gray-400 hover:text-primary-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-center">Click smiley</p>
                    </div>

                    <!-- Category Name Input -->
                    <div class="flex-grow">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" wire:model.live="name" placeholder="e.g. Autism & ADHD"
                            class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        <p class="text-xs text-gray-400 mt-1">Keep it short and descriptive. Max 50 characters.</p>
                    </div>
                </div>

                <!-- Description Input -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span
                            class="text-red-500">*</span></label>
                    <textarea id="description" wire:model.live="description" rows="4"
                        placeholder="Explain what this category is for, e.g., Specific chat about neurodiversity, diagnosis pathways, and sensory processing."
                        class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm resize-none"></textarea>
                    <p class="text-xs text-gray-400 mt-1">This helps users know what they should post here.</p>
                </div>

                <!-- Status Radio Buttons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="status" value="active" wire:model="status" class="peer sr-only"
                                checked>
                            <div
                                class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 transition h-full">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium text-gray-900 text-sm">Active</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Users can view and post in this category.</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="status" value="locked" wire:model="status" class="peer sr-only">
                            <div
                                class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition h-full">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span class="font-medium text-gray-900 text-sm">Locked</span>
                                </div>
                                {{--<p class="text-xs text-gray-500 mt-1">Users can view past posts, but cannot create new
                                    ones.</p>   --}}
                                <p class="text-xs text-gray-500 mt-1">Only admins can see and access this category.</p>
                            </div>
                        </label>
                        {{--<label class="flex-1 cursor-pointer">
                            <input type="radio" name="status" value="hidden" wire:model="status" class="peer sr-only">
                            <div
                                class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-gray-500 peer-checked:bg-gray-50 transition h-full">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <span class="font-medium text-gray-900 text-sm">Hidden</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Only admins can see and access this category.</p>
                            </div>
                        </label>  --}}
                    </div>
                </div>

                <!-- Priority Visibility Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Feed Priority</label>
                    <div class="p-4 border-2 border-gray-200 rounded-xl transition {{ $priorityLimitReached ? 'bg-gray-50 border-dashed opacity-70' : 'hover:border-primary-500' }}">
                        <label class="flex items-start gap-3 {{ $priorityLimitReached ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            <input type="checkbox" wire:model="make_priority" {{ $priorityLimitReached ? 'disabled' : '' }}
                                class="mt-0.5 w-5 h-5 text-primary-600 rounded focus:ring-primary-500 border-gray-300 shrink-0">
                            <div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-primary-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <span class="font-medium text-gray-900 text-sm">Show on Frontend Feed</span>
                                </div>

                                @if($priorityLimitReached)
                                    <p class="text-xs text-red-500 mt-1 font-medium flex items-center">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Limit of 15 priority categories reached. Please uncheck another category first.
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">
                                        Priority categories appear in the sidebar on the main community feed. Disable this to keep it accessible only via the "More Categories" page.
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ 15 - $currentPriorityCount }} out of 15 slots remaining.
                                    </p>
                                @endif
                            </div>
                        </label>
                        @error('make_priority')
                            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('admin.categories') }}" wire:navigate
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-orange-500 rounded-lg font-medium hover:bg-orange-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $categoryId ? 'Update Category' : 'Save Category' }}
                    </button>
                </div>

            </form>
        </div>

        <!-- Right Column: Sidebar Info & Preview -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Live Preview -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Live Preview</h3>
                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50">
                    <div class="flex items-center mb-3">
                        <span class="text-2xl mr-3 shrink-0">{{ $icon }}</span>
                        <!-- Added min-w-0 and flex-1 to allow truncate to work correctly inside flex -->
                        <div class="min-w-0 flex-1">
                            <!-- Added truncate to strictly enforce 1 line -->
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $name ?: 'New Category' }}</p>
                            <p class="text-xs text-gray-400">0 Posts</p>
                        </div>
                        <span
                            class="ml-2 text-xs bg-orange-50 text-orange-700 px-3 py-1 rounded-full font-medium shrink-0">Category</span>
                    </div>
                    <!-- Using Laravel's Str::limit to cap at exactly 225 characters -->
                    <p class="text-gray-500 text-xs leading-relaxed">
                        {{ \Illuminate\Support\Str::limit($description ?: 'Your description will show up here so you can see how it looks on the main forum page.', 225, '...') }}
                    </p>
                </div>
                <p class="text-xs text-gray-400 mt-3 text-center">This updates as you type.</p>
            </div>
        </div>

    </div>

</div>

<!-- ===== EMOJI KEYBOARD SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4/dist/index.min.js"></script>
<script>
    // This function sets up the picker and attaches a document listener
    function initEmojiPicker() {
        // Prevent duplicate listeners if Livewire re-initializes scripts
        if (window.emojiPickerInitialized) return;
        window.emojiPickerInitialized = true;

        const picker = new EmojiButton({
            position: 'right-start',
            autoHide: true
        });

        picker.on('emoji', selection => {
            const input = document.getElementById('icon');
            if (input) {
                input.value = selection.emoji; // Set the emoji in the input
                input.dispatchEvent(new Event('input')); // Tell Livewire to update the state!
            }
        });

        // Event delegation on the document so it survives Livewire DOM morphing
        document.addEventListener('click', function (e) {
            if (e.target.closest('#emoji-picker-btn')) {
                e.preventDefault();
                const btn = e.target.closest('#emoji-picker-btn');
                picker.togglePicker(btn);
            }
        });
    }

    // Run on initial page load
    document.addEventListener('DOMContentLoaded', initEmojiPicker);

    // Run when Livewire navigates to this page using wire:navigate
    document.addEventListener('livewire:navigated', initEmojiPicker);
</script>