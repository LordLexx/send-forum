<?php
// resources/views/livewire/admin/jargon-form.blade.php

use App\Models\JargonTerm;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-layout')] class extends Component {

    public $jargonId = null;
    public $term = '';
    public $full_form = '';
    public $definition = '';
    public $extra_content = '';
    public $status = 'draft';
    public $existing_slug = '';

    // Inject the JargonTerm model directly via route model binding
    public function mount(JargonTerm $jargonTerm = null): void
    {
        // Check if an existing term was passed
        if ($jargonTerm && $jargonTerm->exists) {
            $this->jargonId = $jargonTerm->id;
            $this->term = $jargonTerm->term;
            $this->full_form = $jargonTerm->full_form ?? '';
            $this->definition = $jargonTerm->definition;
            $this->extra_content = $jargonTerm->extra_content ?? '';
            $this->status = $jargonTerm->status;
            $this->existing_slug = $jargonTerm->slug;
        }
    }

    public function save(): void
    {
        $rules = [
            'term' => [
                'required', 'string', 'max:100',
                Rule::unique('jargon_terms', 'term')->ignore($this->jargonId ?? 0),
            ],
            'full_form' => 'nullable|string|max:255',
            'definition' => 'required|string|max:2000',
            'extra_content' => 'nullable|string|max:10000',
            'status' => 'required|in:draft,published',
        ];

        $validated = $this->validate($rules);

        $data = [
            'term' => $validated['term'],
            'full_form' => $validated['full_form'],
            'definition' => $validated['definition'],
            'extra_content' => $validated['extra_content'],
            'status' => $validated['status'],
            'user_id' => auth()->id(),
        ];

        if ($this->jargonId) {
            // Slug is intentionally left untouched on edit (stable public URLs),
            // same behaviour as your event form.
            $jargon = JargonTerm::find($this->jargonId);
            $jargon->update($data);
            $this->dispatch('notify', type: 'success', message: 'Term updated successfully.');
        } else {
            // Generate a unique slug from the term (e.g. "AAC" -> "aac")
            $slug = Str::slug($validated['term']);
            $originalSlug = $slug;
            $count = 1;

            while (JargonTerm::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $data['slug'] = $slug;
            JargonTerm::create($data);
            $this->dispatch('notify', type: 'success', message: 'Term created successfully.');
        }

        $this->redirect(route('admin.jargon'), navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.admin.jargon-form');
    }
};
?>

@section('page_title', $jargonId ? 'Edit Term' : 'Create Term')

<div> <!-- Root element wrapper -->
    <div class="p-6 space-y-6">

        <!-- Page Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $jargonId ? 'Edit Jargon Term' : 'Add New Jargon Term' }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $jargonId ? 'Update this dictionary entry.' : 'Add an entry to the public SEND Jargon Buster.' }}</p>
            </div>
            <a href="{{ route('admin.jargon') }}" wire:navigate
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to All Terms
            </a>
        </div>

        <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- ===== LEFT COLUMN: Main Form ===== -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Term Details Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">

                    <!-- Term Input -->
                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700 mb-1">Term / Acronym <span class="text-red-500">*</span></label>
                        <input type="text" id="term" wire:model.live="term" maxlength="100"
                            placeholder="e.g. AAC"
                            class="w-full h-11 px-4 rounded-lg bg-gray-50 border {{ $errors->has('term') ? 'border-red-300 bg-red-50' : 'border-gray-200' }} focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        <p class="text-xs text-gray-400 mt-1">The short term or acronym parents will hear, e.g. "EHCP" or "Annual Review".</p>
                        @error('term')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Form Input -->
                    <div>
                        <label for="full_form" class="block text-sm font-medium text-gray-700 mb-1">What it stands for <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" id="full_form" wire:model.live="full_form" maxlength="255"
                            placeholder="e.g. Augmentative and Alternative Communication"
                            class="w-full h-11 px-4 rounded-lg bg-gray-50 border {{ $errors->has('full_form') ? 'border-red-300 bg-red-50' : 'border-gray-200' }} focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        <p class="text-xs text-gray-400 mt-1">Shown in bold under the term. Leave blank for plain-word terms like "Additional Needs".</p>
                        @error('full_form')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Definition Input -->
                    <div>
                        <label for="definition" class="block text-sm font-medium text-gray-700 mb-1">Definition <span class="text-red-500">*</span></label>
                        <textarea id="definition" wire:model.live="definition" rows="5" maxlength="2000"
                            placeholder="e.g. Methods of communication used to replace or supplement speech for those with impairments."
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border {{ $errors->has('definition') ? 'border-red-300 bg-red-50' : 'border-gray-200' }} focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm resize-y"></textarea>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-400">Parent-friendly, plain English. Appears on the listing card and the term page.</p>
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ strlen($definition) }}/2000</span>
                        </div>
                        @error('definition')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT COLUMN: Sidebar ===== -->
            <div class="space-y-6">

                <!-- Publish Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="status" wire:model.live="status"
                                class="w-full h-11 pl-4 pr-10 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm appearance-none">
                                <option value="draft">Save as Draft</option>
                                <option value="published">Publish Immediately</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-100 space-y-3">
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="w-full h-11 rounded-lg bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">{{ $jargonId ? 'Update Term' : 'Save Term' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                        <a href="{{ route('admin.jargon') }}" wire:navigate class="block text-center text-sm text-gray-500 hover:text-gray-700 transition">Cancel</a>
                    </div>

                    @if($existing_slug)
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400 mb-1.5">Public URL (auto-generated)</p>
                            <code class="block text-xs text-primary-700 bg-primary-50 border border-primary-100 px-2.5 py-1.5 rounded-lg break-all">/jargon-buster/{{ $existing_slug }}</code>
                        </div>
                    @endif
                </div>

                <!-- Live Preview Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Live Preview</h3>
                    <p class="text-xs text-gray-400 mb-4">How the card will look on the public Jargon Buster page.</p>

                    <!-- Mirrors the frontend .jargon-item card exactly -->
                    <div class="bg-gray-50 border border-gray-100 p-6 rounded-2xl">
                        <h4 class="text-xl font-bold text-primary-700 mb-2 break-words">{{ $term ?: 'Your Term' }}</h4>
                        @if($full_form)
                            <p class="text-sm text-gray-600 font-semibold mb-2">{{ $full_form }}</p>
                        @endif
                        <p class="text-sm text-gray-600">{{ $definition ?: 'Your plain-English definition will appear here…' }}</p>
                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        Term page title: <span class="text-gray-600 font-medium">{{ $term ?: 'Term' }}@if($full_form) – {{ $full_form }}@endif</span>
                    </p>
                </div>

                <!-- Writing Tips -->
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5">
                    <p class="text-xs font-semibold text-orange-700 mb-2">Writing tips</p>
                    <ul class="text-xs text-orange-700/80 space-y-1.5 list-disc list-inside">
                        <li>Avoid using jargon to explain jargon.</li>
                        <li>Keep definitions to one or two sentences.</li>
                        <li>Aim for a reading age of around 11.</li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>