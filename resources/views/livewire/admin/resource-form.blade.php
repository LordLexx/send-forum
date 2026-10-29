<?php

use App\Notifications\NewEvent;
use App\Notifications\NewResource;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Resource;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithFileUploads;

    public ?int $resourceId = null;
    public string $title = '';
    public string $description = '';
    public string $external_url = '';
    public string $status = 'published';

    public bool $is_fathers_hub = false;

    public string $resourceType = 'file';

    public $file = null;
    public ?string $existing_file = '';
    public ?string $existing_file_type = '';
    public ?string $existing_filename = ''; // 👈 NEW
    public bool $showFileUpload = false;

    public function mount($id = null): void
    {
        if ($id) {
            $resource = Resource::findOrFail($id);
            $this->resourceId          = $resource->id;
            $this->title               = $resource->title;
            $this->description         = $resource->description;
            $this->status              = $resource->status;
            $this->resourceType        = $resource->type ?? 'file';
            $this->existing_file       = $resource->file_path ?? '';
            $this->existing_file_type  = $resource->file_type ?? '';
            $this->is_fathers_hub = (bool) $resource->is_fathers_hub;
            $this->existing_filename   = $resource->original_filename ?? ''; // 👈 NEW
            $this->external_url        = $resource->external_url ?? '';
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'is_fathers_hub' => 'boolean',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'resourceType' => 'required|in:file,link',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
            'external_url' => 'nullable|url|max:500|required_if:resourceType,link',
            'status'       => 'required|in:draft,published',
        ]);

        if ($this->resourceType === 'file' && ! $this->file && ! $this->existing_file) {
            $this->addError('file', 'Please upload a document.');
            $this->dispatch('notify', type: 'error', message: 'A document file is required.');
            return;
        }

         $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'status'      => $validated['status'],
            'type'        => $this->resourceType,
            'is_fathers_hub' => $this->is_fathers_hub, // 👈 NEW
            'user_id'     => auth()->id(),
        ];

        if ($this->resourceType === 'file') {
            if ($this->file) {
                if ($this->existing_file) {
                    Storage::disk('public')->delete($this->existing_file);
                }
                $data['file_path']         = $this->file->store('resources', 'public');
                $data['file_type']         = strtolower($this->file->getClientOriginalExtension());
                $data['original_filename'] = $this->file->getClientOriginalName(); // 👈 NEW
                $data['external_url']      = null;
            } else {
                $data['file_path']         = $this->existing_file ?: null;
                $data['file_type']         = $this->existing_file_type ?: null;
                $data['original_filename'] = $this->existing_filename ?: null; // 👈 NEW
                $data['external_url']      = null;
            }
        } else {
            if ($this->existing_file) {
                Storage::disk('public')->delete($this->existing_file);
            }
            $data['external_url']      = $validated['external_url'];
            $data['file_path']         = null;
            $data['file_type']         = null;
            $data['original_filename'] = null; // 👈 NEW
        }

        if ($this->resourceId) {
            $resource = Resource::find($this->resourceId);
            $resource->update($data);
            $this->dispatch('notify', type: 'success', message: 'Resource updated successfully.');
        } else {
            Resource::create($data);
            $this->dispatch('notify', type: 'success', message: 'Resource created successfully.');
        }

        $this->redirect(route('admin.resources'), navigate: true);
    }
}; ?>

@section('page_title', $resourceId ? 'Edit Resource' : 'Create Resource')

<div>
    <div class="space-y-6">

        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.resources') }}" wire:navigate class="text-gray-500 hover:text-primary-600 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $resourceId ? 'Edit Resource' : 'Create New Resource' }}</h2>
                    <p class="text-sm text-gray-500 hidden sm:block">Upload a document or link a video for members</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6" enctype="multipart/form-data">

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Resource Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" wire:model.live="title" placeholder="e.g. EHCP Parental Views Workbook" class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" wire:model.live="description" rows="4" placeholder="Briefly explain what this resource is, who wrote it, and how it helps parent carers." class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm resize-none"></textarea>
                        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Resource Content (File OR Link) -->
                    <div class="pt-6 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Resource Content <span class="text-red-500">*</span></label>

                        <div class="flex items-center space-x-2 mb-6">
                            <button type="button" wire:click="$set('resourceType', 'file')" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 rounded-lg border text-sm font-medium transition {{ $resourceType === 'file' ? 'bg-primary-600 text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                Upload Document
                            </button>
                            <button type="button" wire:click="$set('resourceType', 'link')" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 rounded-lg border text-sm font-medium transition {{ $resourceType === 'link' ? 'bg-primary-600 text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                External Link
                            </button>
                        </div>

                        @if($resourceType === 'file')
                        <div class="space-y-4 animate-fade-in">
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 mb-1">Document Upload</h3>
                                <p class="text-xs text-gray-500 mb-4">For PDFs, Word Docs, or images. Please keep file sizes small (Max 10MB).</p>

                                <div class="flex flex-col items-center justify-center w-full">

                                    {{-- 1. New file staged --}}
                                    @if ($file)
                                        <div class="w-full mb-4 p-4 bg-gray-50 rounded-xl flex items-center justify-between">
                                            <div class="flex items-center overflow-hidden">
                                                <svg class="h-8 w-8 text-primary-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                <span class="text-sm font-medium text-gray-700 truncate">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                            <button type="button" wire:click="$set('file', null)" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shrink-0 ml-3">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                        <div wire:loading class="text-xs text-primary-600">Uploading file…</div>

                                    {{-- 2. Existing file in DB (with download link + original filename) --}}
                                    @elseif ($existing_file && ! $showFileUpload)
                                        <div class="w-full mb-4 p-4 bg-gray-50 rounded-xl flex items-center justify-between">
                                            <div class="flex items-center overflow-hidden">
                                                <svg class="h-8 w-8 text-primary-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                <a href="{{ route('admin.resources.download', $resourceId) }}"
                                                   download
                                                   class="text-sm font-medium text-primary-700 hover:text-primary-900 underline truncate inline-flex items-center">
                                                    {{-- Use original filename if we have it, fallback to basename --}}
                                                    {{ $existing_filename ?: basename($existing_file) }}
                                                    <svg class="h-4 w-4 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </a>
                                            </div>
                                            <button type="button" wire:click="$set('showFileUpload', true)" class="text-sm text-primary-600 hover:text-primary-900 font-medium shrink-0 ml-3">Replace</button>
                                        </div>

                                    {{-- 3. Dropzone --}}
                                    @else
                                        @if ($existing_file)
                                            <button type="button" wire:click="$set('showFileUpload', false)" class="w-full mb-3 inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition text-sm">Cancel Replace</button>
                                        @endif
                                        <label for="dropzone-file"
                                            x-data="{ dragging: false }"
                                            @dragover.prevent="dragging = true"
                                            @dragleave.prevent="dragging = false"
                                            @drop.prevent="dragging = false; $wire.upload('file', $event.dataTransfer.files[0])"
                                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-200 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-gray-400">PDF, DOC, DOCX, PNG, JPG up to 10MB</p>
                                            </div>
                                            <input id="dropzone-file" type="file" wire:model="file" class="hidden" />
                                        </label>
                                    @endif

                                    @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($resourceType === 'link')
                        <div class="space-y-4 animate-fade-in">
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 mb-1">Video or Web Link</h3>
                                <p class="text-xs text-gray-500 mb-3">For videos, please use a YouTube or Vimeo link. Do not upload video files directly.</p>

                                <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">External URL <span class="text-red-500">*</span></label>
                                <input type="url" id="video_url" wire:model.live="external_url" placeholder="https://youtube.com/watch?v=..." class="w-full h-11 px-4 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                                @error('external_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Fathers Hub Toggle -->
                    <div class="pt-6 border-t border-gray-100">
                        <label class="flex items-start justify-between gap-3 cursor-pointer p-4 bg-emerald-50 border-2 border-emerald-100 rounded-xl">
                            <div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                    <span class="font-medium text-gray-900 text-sm">Fathers Hub Item</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Show this resource on the dedicated Fathers Hub page.</p>
                            </div>
                            <button type="button" wire:click="$toggle('is_fathers_hub')"
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $is_fathers_hub ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition {{ $is_fathers_hub ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </label>
                    </div>

                    <!-- Status -->
                    <div class="pt-6 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="status" value="published" class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 transition h-full">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="font-medium text-gray-900 text-sm">Publish Immediately</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Resource will be visible to all members right away.</p>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model.live="status" value="draft" class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition h-full">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span class="font-medium text-gray-900 text-sm">Save as Draft</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Save for later. Only admins will see this resource.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('admin.resources') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-200 text-gray-700 bg-white rounded-lg font-medium hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-white bg-primary-600 rounded-lg font-medium hover:bg-primary-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            {{ $resourceId ? 'Update Resource' : 'Save Resource' }}
                        </button>
                    </div>

                </form>
            </div>

            <!-- Right Column: Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Resource Tips</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-gray-600">Always check documents for personal data before uploading.</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-gray-600">If a PDF is too large, use a free tool to compress it below 10MB.</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-gray-600">For videos, upload to YouTube as "Unlisted" and paste the link here.</span>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-6">
                    <div class="flex items-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <h3 class="font-bold text-orange-800 text-sm">File Security</h3>
                    </div>
                    <p class="text-orange-700 text-xs leading-relaxed">Ensure you have the right to share this document and that it doesn't contain sensitive information about other families.</p>
                </div>
            </div>

        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>
