<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendNewsletterJob;
use Illuminate\Support\Facades\Storage;
use App\Models\NewsletterSubscriber;

new #[Layout('layouts.blank')] class extends Component {
    public ?int $newsletterId = null;
    public ?array $existingBlocks = [];
    public ?array $existingStyle = [];
    public string $existingTitle = '';
    public string $existingSubject = '';
    public string $existingPreviewText = '';
    public array $existingDataArray = [];

    public function mount($newsletter = null): void
    {
        if ($newsletter) {
            $n = Newsletter::findOrFail($newsletter);
            $this->newsletterId       = $n->id;
            $this->existingTitle      = $n->title ?? '';
            $this->existingSubject    = $n->subject ?? '';
            $this->existingPreviewText = $n->preview_text ?? '';
            $this->existingBlocks     = $n->blocks ?? [];
            $this->existingStyle      = $n->style_config ?? [];
        }

        $this->existingDataArray = [
            'newsletterId' => $this->newsletterId,
            'title' => $this->existingTitle,
            'subject' => $this->existingSubject,
            'previewText' => $this->existingPreviewText,
            'blocks' => $this->existingBlocks,
            'style' => $this->existingStyle,
            'status' => isset($n) ? $n->status : 'new',
        ];
    }

    public function save(array $data): array
    {
        $newsletter = Newsletter::updateOrCreate(
            ['id' => $this->newsletterId],
            [
                'title'           => $data['title'] ?? 'Untitled Newsletter',
                'subject'         => $data['subject'] ?? ($data['title'] ?? ''),
                'preview_text'    => $data['preview_text'] ?? '',
                'blocks'          => $data['blocks'] ?? [],
                'style_config'    => $data['style'] ?? [],
                'status'          => 'draft',
                'created_by'      => Auth::id(),
                'recipient_group' => $data['recipient_group'] ?? 'all',
                'sender_name'     => $data['sender_name'] ?? 'Slough SEND PCF',
                'reply_to'        => $data['reply_to'] ?? 'hello@sloughsend.org.uk',
                'track_opens'     => $data['track_opens'] ?? true,
                'track_clicks'    => $data['track_clicks'] ?? true,
            ]
        );

        $this->newsletterId = $newsletter->id;
        $this->skipRender();

        return ['id' => $newsletter->id];
    }

    public function send(array $data): array
    {
        $newsletter = Newsletter::updateOrCreate(
            ['id' => $this->newsletterId],
            [
                'title'           => $data['title'] ?? 'Untitled Newsletter',
                'subject'         => $data['subject'] ?? ($data['title'] ?? ''),
                'preview_text'    => $data['preview_text'] ?? '',
                'blocks'          => $data['blocks'] ?? [],
                'style_config'    => $data['style'] ?? [],
                'created_by'      => Auth::id(),
                'recipient_group' => $data['recipient_group'] ?? 'all',
                'sender_name'     => $data['sender_name'] ?? 'Slough SEND PCF',
                'reply_to'        => $data['reply_to'] ?? 'hello@sloughsend.org.uk',
                'track_opens'     => $data['track_opens'] ?? true,
                'track_clicks'    => $data['track_clicks'] ?? true,
            ]
        );

        $this->newsletterId = $newsletter->id;

        $recipients = $this->getRecipients($data['recipient_group'] ?? 'all');

        try {
            SendNewsletterJob::dispatchSync($newsletter->id, $recipients);
        } catch (\Exception $e) {
            logger()->error('Newsletter Send Error: ' . $e->getMessage());
            throw new \Exception('Failed to send newsletter. The email provider returned an error (e.g., rate limit or authentication issue). Please try again.');
        }

        $newsletter->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);

        $this->skipRender();

        return ['count' => count($recipients), 'id' => $newsletter->id];
    }

    private function getRecipients(string $group): array
    {
        $userEmails = match ($group) {
            'all', 'all_subscribers' => User::where('status', 'approved')->pluck('email')->unique(),
            'staff'    => User::whereIn('role', ['admin', 'moderator'])->pluck('email')->unique(),
            'self'     => collect([auth()->user()->email]),
            default    => collect(),
        };

        // If the group includes public subscribers, fetch and merge them
        if (in_array($group, ['all_subscribers', 'subscribers'])) {
            // UPDATED: Only fetch subscribers where 'is_verified' is true
            $subscriberEmails = \App\Models\NewsletterSubscriber::where('is_verified', true)->pluck('email')->unique();
            return $userEmails->merge($subscriberEmails)->unique()->values()->toArray();
        }

        return $userEmails->values()->toArray();
    }
};
?>

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        50: '#f2f3f8',
                        100: '#e6e8f1',
                        200: '#c5cce6',
                        300: '#9aa6d1',
                        400: '#6a78b5',
                        500: '#3f4d99',
                        600: '#2b3a7a',
                        700: '#141f52',
                        800: '#101944',
                        900: '#0d153b',
                    }
                }
            }
        }
    }
</script>
<style>
    body { font-family: 'Open Sans', sans-serif; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
    .gradient-bg { background: linear-gradient(135deg, #141f52 0%, #2b3a7a 100%); }
    .editor-block { position: relative; transition: all 0.2s ease; border-radius: 8px; padding: 8px 12px; margin: 8px 0; border: 1px solid transparent; }
    .editor-block:hover { background: rgba(0,0,0,0.02); border-color: #f3f4f6; }
    .editor-block.selected { background: rgba(20, 31, 82, 0.06); border-color: #c5cce6; box-shadow: inset 3px 0 0 #141f52; }
    .block-controls { opacity: 0; transition: opacity 0.15s ease; pointer-events: none; }
    .editor-block:hover .block-controls, .editor-block.selected .block-controls { opacity: 100; pointer-events: auto; }
    .editor-block.dragging { opacity: 0.5; transform: scale(0.98); }
    .editor-block.drag-over-top { box-shadow: 0 -3px 0 0 #141f52; border-color: transparent !important; }
    .editor-block.drag-over-bottom { box-shadow: 0 3px 0 0 #141f52; border-color: transparent !important; }
    .add-block-btn { opacity: 0; transition: opacity 0.15s ease; }
    .add-block-wrapper:hover .add-block-btn, .add-block-btn.visible { opacity: 1; }
    [contenteditable="true"]:empty:before { content: attr(data-placeholder); color: #9ca3af; pointer-events: none; display: block; }
    [contenteditable="true"]:focus { outline: none; }
    .slash-menu { animation: fadeIn 0.15s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .newsletter-preview { font-family: var(--body-font, 'Open Sans'); line-height: var(--line-height, 1.6); font-size: var(--base-font-size, 15px); color: var(--body-text-color, #334155); }
    .newsletter-preview h1, .newsletter-preview h2, .newsletter-preview h3, .newsletter-preview h4, .newsletter-preview h5, .newsletter-preview h6 { font-family: var(--heading-font, 'Poppins'); color: var(--heading-color, #0f172a); margin: 0.6em 0 0.4em; font-weight: 700; }
    .newsletter-preview h1 { font-size: calc(1.75rem * var(--heading-scale, 1)); }
    .newsletter-preview h2 { font-size: calc(1.5rem * var(--heading-scale, 1)); }
    .newsletter-preview h3 { font-size: calc(1.25rem * var(--heading-scale, 1)); font-weight: 600; }
    .newsletter-preview h4 { font-size: calc(1.125rem * var(--heading-scale, 1)); font-weight: 600; }
    .newsletter-preview h5 { font-size: calc(1rem * var(--heading-scale, 1)); font-weight: 600; }
    .newsletter-preview h6 { font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted-color, #475569); }
    .newsletter-preview p { margin: 0 0 var(--block-spacing, 16px); color: var(--body-text-color, #334155); }
    .newsletter-preview ul { list-style: disc; padding-left: 1.5em; margin: 0 0 var(--block-spacing, 16px); }
    .newsletter-preview ol { list-style: decimal; padding-left: 1.5em; margin: 0 0 var(--block-spacing, 16px); }
    .newsletter-preview li { margin: 0.25em 0; color: var(--body-text-color, #334155); }
    .newsletter-preview blockquote { border-left: 4px solid var(--primary-color, #141f52); background: var(--blockquote-bg, #e6e8f1); color: var(--muted-color, #475569); border-radius: 0 var(--border-radius, 10px) var(--border-radius, 10px) 0; padding: 0.5em 1em; margin: 0 0 var(--block-spacing, 16px); font-style: italic; }
    .newsletter-preview img { max-width: 100%; border-radius: var(--border-radius, 10px); margin: 0 0 var(--block-spacing, 16px); display: block; }
    .newsletter-preview hr { border: none; border-top: 1px solid var(--border-color, #e2e8f0); margin: 1.2em 0; }
    .newsletter-preview a { color: var(--link-color, #141f52); text-decoration: underline; }
    .newsletter-preview .cta-button { display: inline-block; background: var(--accent-color, #f97316); color: var(--text-on-primary, #ffffff); padding: 10px 22px; border-radius: var(--button-radius, 999px); text-decoration: none !important; font-weight: 600; margin: 0.8em 0; }
    .newsletter-preview .embed-wrapper { margin: 0 0 var(--block-spacing, 16px); }
    .newsletter-preview .embed-wrapper iframe { width: 100%; border-radius: var(--border-radius, 10px); min-height: 280px; border: none; }
    .email-header-gradient { background: linear-gradient(135deg, var(--header-bg-color, #141f52) 0%, var(--header-gradient-end, #2b3a7a) 100%); }
    .email-header-solid { background: var(--header-bg-color, #141f52); }
    .email-header-minimal { background: var(--background, #ffffff); border-bottom: 2px solid var(--primary-color, #141f52); }
    .email-header-logo { background: var(--background, #ffffff); border-bottom: 1px solid var(--border-color, #e2e8f0); }
    .email-footer { background: var(--footer-bg-color, #0f172a); color: var(--footer-text-color, #94a3b8); }
    .email-footer a { color: var(--footer-link-color, #6a78b5); }
    .tab-btn { color: #64748b; }
    .tab-btn.active { background: #141f52; color: white; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .device-mobile { max-width: 380px; margin: 0 auto; }
    .device-desktop { max-width: 100%; }
    .preset-swatch { width: 36px; height: 36px; border-radius: 8px; border: 2px solid #fff; box-shadow: 0 0 0 1px #e2e8f0; cursor: pointer; transition: transform 0.1s; }
    .preset-swatch:hover { transform: scale(1.1); }
    .color-field input[type="color"] { cursor: pointer; }
    .preview-block-item { transition: outline 0.1s ease; }
    .preview-block-selected { outline: 2px solid #141f52; outline-offset: 4px; border-radius: 4px; }
    .editor-block p a, .editor-block figcaption a, .editor-block ul a, .editor-block ol a, .editor-block blockquote a {color: #141f52; text-decoration: underline; cursor: pointer;}
    #pane-preview.preview-fullscreen { position: fixed !important; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 9999; background: #f1f5f9; border-radius: 0; display: flex; flex-direction: column; padding: 16px; }
    #pane-preview.preview-fullscreen #previewScrollArea { max-height: none !important; flex-grow: 1; border-radius: 16px; }
    #pane-preview.preview-fullscreen #previewWrapper { max-width: var(--content-width, 600px); margin-left: auto; margin-right: auto; width: 100%; }
    #fullscreenBackBtn { display: none !important; }
    #mainHeader.header-fullscreen-active { z-index: 10001 !important; }
    #mainHeader.header-fullscreen-active #fullscreenBackBtn { display: inline-flex !important; }
    #pane-preview.preview-fullscreen #fullscreenBackBtn { display: flex !important; position: fixed; top: 24px; left: 24px; z-index: 10001; align-items: center; }
</style>
@endpush

<div class="newsletter-editor-root">
    <div class="flex min-h-screen">
        <main class="flex-grow w-full">
            <header id="mainHeader" class="bg-white border-b border-gray-100 h-20 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.newsletters') }}" class="text-gray-500 hover:text-primary-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <img src="{{ asset('build/assets/images/logo/sspcf_logo.png') }}" alt="Slough SEND PCF Logo">
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-bold text-gray-900">{{ $newsletterId ? 'Edit Newsletter' : 'Create Newsletter' }}</h1>
                            <p class="text-xs sm:text-sm text-gray-500 hidden sm:block">Compose, preview and send to your community</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="fullscreenBackBtn" onclick="toggleFullscreenPreview()" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Editor
                    </button>
                    <span id="saveStatus" class="hidden sm:inline-flex items-center text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                        <span id="saveStatusText">Saved as draft</span>
                    </span>
                    <button onclick="saveDraft()" class="hidden sm:inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        Save Draft
                    </button>
                    <button onclick="openSendModal()" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        Send
                    </button>
                    
                    <div class="flex items-center space-x-2 pl-2 border-l border-gray-200">
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                @if (auth()->user()->profile_photo_path)
                                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-primary-200">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border-2 border-primary-200">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="hidden sm:block text-left">
                                    <p class="font-semibold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>
                                <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="font-semibold text-sm text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>

                                <a href="{{ route('feed') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                    View Site
                                </a>

                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    My Profile
                                </a>

                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('profile.edit') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit Profile
                                </a>

                                <a href="{{ route('saved.posts') }}" class="flex items-center px-4 py-2 text-sm {{ request()->routeIs('saved.posts') ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                    Saved Posts
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <div class="p-4 sm:p-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-6 inline-flex">
                    <button onclick="setMode('write')" id="mode-write" class="px-6 py-2 rounded-lg text-sm font-semibold bg-primary-600 text-white inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Write
                    </button>
                    <button onclick="setMode('style')" id="mode-style" class="px-6 py-2 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                        Style
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="lg:col-span-2">
                            <label for="newsletterTitle" class="block text-sm font-medium text-gray-700 mb-1">Newsletter Title <span class="text-red-500">*</span></label>
                            <input type="text" id="newsletterTitle" placeholder="e.g. October Update — SEND News & Resources" oninput="updatePreview()" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                        </div>
                        <div>
                            <label for="emailSubject" class="block text-sm font-medium text-gray-700 mb-1">Email Subject Line <span class="text-red-500">*</span></label>
                            <input type="text" id="emailSubject" placeholder="Slough SEND PCF — October Update" oninput="updatePreview()" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                            <p class="text-xs text-gray-400 mt-1">This appears in the recipient's inbox.</p>
                        </div>
                        <div>
                            <label for="previewText" class="block text-sm font-medium text-gray-700 mb-1">Preview Text</label>
                            <input type="text" id="previewText" placeholder="Latest news, events and resources from the forum..." oninput="updatePreview()" maxlength="140" class="w-full px-4 py-2.5 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
                            <p class="text-xs text-gray-400 mt-1"><span id="previewCount">0</span>/140 — Shows next to the subject line in most inboxes.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <div class="lg:col-span-3 space-y-4">
                        <div id="writePanel">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center justify-between flex-wrap gap-3 mb-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span>
                                    <h2 class="font-bold text-gray-900">Block Editor</h2>
                                    <span id="blockCount" class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold">0 blocks</span>
                                </div>
                                <button onclick="addBlock('paragraph')" class="inline-flex items-center text-xs px-3 py-1.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Block
                                </button>
                            </div>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                                <div id="blockEditor" class="space-y-3 min-h-[400px]"></div>
                                <div class="add-block-wrapper relative mt-6 pt-4 border-t border-gray-100">
                                    <button onclick="openSlashMenu(this)" class="add-block-btn w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 hover:border-primary-400 hover:text-primary-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        <span class="text-sm font-medium">Add a block</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="stylePanel" class="hidden">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                                    <h2 class="font-bold text-gray-900">Newsletter Style</h2>
                                </div>
                                <button onclick="resetStyle()" class="text-xs text-gray-500 hover:text-red-500">Reset to default</button>
                            </div>

                            <div id="blockStylePanel" class="hidden bg-primary-50 border border-primary-200 rounded-2xl shadow-sm p-5 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-primary-900 text-sm">Selected Block Style</h3>
                                    <button onclick="clearPreviewBlockSelection()" class="text-xs text-primary-700 hover:text-primary-900">Clear selection</button>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="color-field"><span class="block text-xs font-medium text-primary-800 mb-1">Block BG</span><input type="color" id="blockBgColor" value="#ffffff" class="w-full h-9 rounded border border-primary-200"></label>
                                        <div><label class="block text-xs font-medium text-primary-800 mb-1">Text Align</label><select id="blockAlign" class="w-full px-3 py-2 rounded-lg bg-white border border-primary-200 text-sm"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="color-field"><span class="block text-xs font-medium text-primary-800 mb-1">Text Color</span><input type="color" id="blockTextColor" value="#334155" class="w-full h-9 rounded border border-primary-200"></label>
                                        <div><label class="block text-xs font-medium text-primary-800 mb-1">Text Font</label><select id="blockFont" class="w-full px-3 py-2 rounded-lg bg-white border border-primary-200 text-sm"><option value="inherit">Inherit Global</option><option value="Poppins">Poppins</option><option value="Open Sans">Open Sans</option><option value="Inter">Inter</option><option value="Lora">Lora</option><option value="Montserrat">Montserrat</option><option value="Playfair Display">Playfair Display</option></select></div>
                                    </div>
                                    <div><label class="block text-xs font-medium text-primary-800 mb-1">Padding — <span id="blockPaddingLabel">0</span>px</label><input type="range" min="0" max="40" value="0" id="blockPadding" class="w-full accent-primary-600"></div>

                                    <div id="buttonBlockControls" class="hidden pt-4 mt-2 border-t border-primary-200 space-y-4">
                                        <p class="text-xs font-bold text-primary-700 uppercase tracking-wider">Button Settings</p>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label class="color-field"><span class="block text-xs font-medium text-primary-800 mb-1">Button Color</span><input type="color" id="buttonColor" value="#f97316" class="w-full h-9 rounded border border-primary-200"></label>
                                            <div><label class="block text-xs font-medium text-primary-800 mb-1">Button Size</label><select id="buttonSize" class="w-full px-3 py-2 rounded-lg bg-white border border-primary-200 text-sm"><option value="small">Small</option><option value="medium" selected>Medium</option><option value="large">Large</option></select></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <details class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4" open>
                                <summary class="cursor-pointer font-bold text-gray-900 text-sm flex items-center justify-between"><span>🎨 Colors</span><span class="text-xs text-gray-400 font-normal">Click to expand</span></summary>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Page Color</span><input type="color" data-style-key="background" value="#ffffff" class="w-full h-9 rounded border border-gray-200"></label>
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Accent / Button</span><input type="color" data-style-key="accentColor" value="#f97316" class="w-full h-9 rounded border border-gray-200"></label>
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Links</span><input type="color" data-style-key="linkColor" value="#141f52" class="w-full h-9 rounded border border-gray-200"></label>
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Body Text</span><input type="color" data-style-key="bodyTextColor" value="#334155" class="w-full h-9 rounded border border-gray-200"></label>
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Headings</span><input type="color" data-style-key="headingColor" value="#0f172a" class="w-full h-9 rounded border border-gray-200"></label>
                                    <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Borders / Lines</span><input type="color" data-style-key="borderColor" value="#e2e8f0" class="w-full h-9 rounded border border-gray-200"></label>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Theme Presets</p>
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <button onclick="applyPreset('teal')" class="preset-swatch" style="background:linear-gradient(135deg,#141f52,#2b3a7a)"></button>
                                        <button onclick="applyPreset('purple')" class="preset-swatch" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)"></button>
                                        <button onclick="applyPreset('blue')" class="preset-swatch" style="background:linear-gradient(135deg,#1d4ed8,#60a5fa)"></button>
                                        <button onclick="applyPreset('rose')" class="preset-swatch" style="background:linear-gradient(135deg,#be123c,#fb7185)"></button>
                                        <button onclick="applyPreset('forest')" class="preset-swatch" style="background:linear-gradient(135deg,#166534,#86efac)"></button>
                                        <button onclick="applyPreset('slate')" class="preset-swatch" style="background:linear-gradient(135deg,#334155,#94a3b8)"></button>
                                    </div>
                                    <div class="flex items-end space-x-2">
                                        <label class="color-field flex-1"><span class="block text-xs font-medium text-gray-600 mb-1">Custom Theme Color</span><input type="color" id="customThemeColor" value="#141f52" class="w-full h-9 rounded border border-gray-200"></label>
                                        <button onclick="applyCustomTheme()" class="px-4 py-2 bg-primary-600 text-white text-xs rounded-lg hover:bg-primary-700 h-9">Apply Color</button>
                                    </div>
                                </div>
                            </details>

                            <details class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4" open>
                                <summary class="cursor-pointer font-bold text-gray-900 text-sm">🔤 Typography</summary>
                                <div class="space-y-4 mt-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Heading Font</label><select data-style-key="headingFont" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"><option value="Poppins">Poppins</option><option value="Playfair Display">Playfair Display</option><option value="Montserrat">Montserrat</option><option value="Lora">Lora</option><option value="Merriweather">Merriweather</option><option value="Inter">Inter</option><option value="Georgia">Georgia (system)</option><option value="Arial">Arial (system)</option></select></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Body Font</label><select data-style-key="bodyFont" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"><option value="Open Sans">Open Sans</option><option value="Inter">Inter</option><option value="Roboto">Roboto</option><option value="Lato">Lato</option><option value="Nunito Sans">Nunito Sans</option><option value="Helvetica">Helvetica (system)</option></select></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Base Font Size — <span id="fontSizeLabel">15</span>px</label><input type="range" min="12" max="20" value="15" data-style-key="baseFontSize" class="w-full accent-primary-600"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Heading Scale — <span id="headingScaleLabel">1.0</span>x</label><input type="range" min="0.8" max="1.4" step="0.05" value="1.0" data-style-key="headingScale" class="w-full accent-primary-600"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Line Height — <span id="lineHeightLabel">1.6</span></label><input type="range" min="1.2" max="2.0" step="0.1" value="1.6" data-style-key="lineHeight" class="w-full accent-primary-600"></div>
                                </div>
                            </details>

                            <details class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                                <summary class="cursor-pointer font-bold text-gray-900 text-sm">📐 Spacing & Layout</summary>
                                <div class="space-y-4 mt-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Content Padding — <span id="paddingLabel">32</span>px</label><input type="range" min="16" max="60" value="32" data-style-key="contentPadding" class="w-full accent-primary-600"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Block Spacing — <span id="blockSpacingLabel">16</span>px</label><input type="range" min="8" max="40" value="16" data-style-key="blockSpacing" class="w-full accent-primary-600"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Corner Radius — <span id="radiusLabel">10</span>px</label><input type="range" min="0" max="24" value="10" data-style-key="borderRadius" class="w-full accent-primary-600"></div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Button Shape</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="cursor-pointer"><input type="radio" name="buttonRadius" value="999" data-style-key="buttonRadius" class="sr-only peer" checked><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-xs font-medium text-gray-700">Pill</div></label>
                                            <label class="cursor-pointer"><input type="radio" name="buttonRadius" value="8" data-style-key="buttonRadius" class="sr-only peer"><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-xs font-medium text-gray-700">Rounded</div></label>
                                            <label class="cursor-pointer"><input type="radio" name="buttonRadius" value="0" data-style-key="buttonRadius" class="sr-only peer"><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-xs font-medium text-gray-700">Square</div></label>
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <details class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                                <summary class="cursor-pointer font-bold text-gray-900 text-sm">📧 Email Header</summary>
                                <div class="space-y-4 mt-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Header Style</label>
                                        <div class="grid grid-cols-4 gap-2">
                                            <label class="cursor-pointer"><input type="radio" name="headerStyle" value="gradient" data-style-key="headerStyle" class="sr-only peer" checked><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-[10px] font-medium text-gray-700">Gradient</div></label>
                                            <label class="cursor-pointer"><input type="radio" name="headerStyle" value="solid" data-style-key="headerStyle" class="sr-only peer"><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-[10px] font-medium text-gray-700">Solid</div></label>
                                            <label class="cursor-pointer"><input type="radio" name="headerStyle" value="minimal" data-style-key="headerStyle" class="sr-only peer"><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-[10px] font-medium text-gray-700">Minimal</div></label>
                                            <label class="cursor-pointer"><input type="radio" name="headerStyle" value="logo" data-style-key="headerStyle" class="sr-only peer"><div class="py-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-[10px] font-medium text-gray-700">Logo</div></label>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label><span class="block text-xs font-medium text-gray-600 mb-1">Header Text</span><input type="text" data-style-key="headerText" value="Slough SEND Parent Carer Forum" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></label>
                                        <label><span class="block text-xs font-medium text-gray-600 mb-1">Logo Text (Fallback)</span><input type="text" data-style-key="headerLogoText" value="S" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Header Logo</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="headerLogoUrlInput" data-style-key="headerLogoUrl" value="/build/assets/images/logo/sspcf_logo.png" placeholder="Paste URL or upload" class="flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            <button type="button" onclick="document.getElementById('headerLogoFileInput').click()" class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm hover:bg-gray-200 transition whitespace-nowrap flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                Upload
                                            </button>
                                            <input type="file" id="headerLogoFileInput" accept="image/*" class="hidden" onchange="handleHeaderLogoUpload(event)">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Upload from device or paste a public URL. Leave empty to use text fallback.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Header BG</span><input type="color" data-style-key="headerBgColor" value="#141f52" class="w-full h-9 rounded border border-gray-200"></label>
                                        <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Gradient End</span><input type="color" data-style-key="headerGradientEnd" value="#2b3a7a" class="w-full h-9 rounded border border-gray-200"></label>
                                    </div>
                                </div>
                            </details>

                            <details class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                                <summary class="cursor-pointer font-bold text-gray-900 text-sm">🔚 Email Footer</summary>
                                <div class="space-y-4 mt-4">
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Organization Name</label><input type="text" data-style-key="footerOrgName" value="Slough SEND Parent Carer Forum" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></div>
                                    
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Tagline Title</label><input type="text" data-style-key="footerTagline" value="Every Voice Matters" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Tagline Subtitle</label><input type="text" data-style-key="footerSubTagline" value="Listening • Supporting • Representing • Influencing • Empowering" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm"></div>
                                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Footer Message</label><textarea data-style-key="footerText" rows="2" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">Together, we are creating a stronger future for SEND families across Slough. 💙</textarea></div>

                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Contact & Social Links</p>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Website Text</label>
                                                <input type="text" data-style-key="footerWebsiteText" value="www.sloughsendpcf.org" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm mb-1">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Website URL</label>
                                                <input type="text" data-style-key="footerWebsiteUrl" value="https://www.sloughsendpcf.org" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Email Address</label>
                                                <input type="text" data-style-key="footerEmail" value="admin@sloughsendpcf.org" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Facebook Text</label>
                                                <input type="text" data-style-key="footerFacebookText" value="Slough SEND Parent Carer Forum" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm mb-1">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Facebook URL</label>
                                                <input type="text" data-style-key="footerFacebookUrl" value="https://www.facebook.com/" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Instagram Text</label>
                                                <input type="text" data-style-key="footerInstagramText" value="@sloughsendpcf" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm mb-1">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Instagram URL</label>
                                                <input type="text" data-style-key="footerInstagramUrl" value="https://www.instagram.com/sloughsendpcf" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Location / Address</label>
                                                <input type="text" data-style-key="footerLocation" value="Slough SEND Parent Carer Forum, Slough, UK" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-100 pt-4">
                                        <div class="grid grid-cols-3 gap-4">
                                            <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">BG Color</span><input type="color" data-style-key="footerBgColor" value="#0f172a" class="w-full h-9 rounded border border-gray-200"></label>
                                            <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Text Color</span><input type="color" data-style-key="footerTextColor" value="#94a3b8" class="w-full h-9 rounded border border-gray-200"></label>
                                            <label class="color-field"><span class="block text-xs font-medium text-gray-600 mb-1">Link Color</span><input type="color" data-style-key="footerLinkColor" value="#6a78b5" class="w-full h-9 rounded border border-gray-200"></label>
                                        </div>
                                    </div>
                                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer"><span class="text-sm text-gray-700">Show unsubscribe link</span><input type="checkbox" data-style-key="showUnsubscribe" checked class="accent-primary-600 w-4 h-4"></label>
                                </div>
                            </details>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-24 lg:self-start lg:max-h-[calc(100vh-6rem)] lg:overflow-y-auto custom-scroll pb-4">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 flex items-center space-x-1">
                            <button onclick="switchTab('preview')" id="tab-preview" class="tab-btn active flex-1 py-2 rounded-lg text-sm font-medium transition">Preview</button>
                            <button onclick="switchTab('recipients')" id="tab-recipients" class="tab-btn flex-1 py-2 rounded-lg text-sm font-medium transition">Recipients</button>
                            <button onclick="switchTab('settings')" id="tab-settings" class="tab-btn flex-1 py-2 rounded-lg text-sm font-medium transition">Settings</button>
                        </div>

                        <div id="pane-preview" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 relative">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 text-sm">Live Preview</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="flex bg-gray-100 rounded-lg p-0.5">
                                        <button onclick="setPreviewDevice('desktop')" id="deviceDesktop" class="p-1.5 rounded text-gray-500 hover:text-gray-700 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></button>
                                        <button onclick="setPreviewDevice('mobile')" id="deviceMobile" class="p-1.5 rounded text-gray-500 hover:text-gray-700 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg></button>
                                    </div>
                                    <button onclick="toggleFullscreenPreview()" id="fullscreenBtn" class="p-1.5 rounded text-gray-500 hover:text-gray-700 transition bg-gray-100">
                                        <svg id="maximizeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l4 4m8-4h4m0 0v4m0-4l-4 4M4 16v4m0 0h4m-4 0l4-4m8 4l4-4m0 0v-4m0 4h-4" /></svg>
                                        <svg id="minimizeIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4H4m0 0l4 4M15 9V4h5m0 0l-4 4M9 15v5H4m0 0l4-4M15 15v5h5m0 0l-4-4" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div id="previewScrollArea" class="bg-gray-50 rounded-xl p-4 max-h-[600px] overflow-y-auto custom-scroll">
                                <div id="previewWrapper" class="device-desktop bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100">
                                    <div class="email-header email-header-gradient p-5 text-white text-center"></div>
                                    <div class="email-content mx-auto" style="max-width:600px;">
                                        <div class="p-5">
                                            <h1 id="previewTitle" class="text-xl font-bold text-gray-900 mb-2" style="font-family: 'Poppins', sans-serif;">Your newsletter title</h1>
                                            <p id="previewDate" class="text-xs text-gray-400 mb-4"></p>
                                            <div id="previewContent" class="newsletter-preview">
                                                <p class="text-gray-400 italic">Start adding blocks to see your newsletter come to life...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="email-footer p-5 text-center text-xs"></div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-3 text-center">Preview updates as you type.</p>
                        </div>

                        <div id="pane-recipients" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm mb-1">Choose recipients</h3>
                                <p class="text-xs text-gray-500">Select who should receive this newsletter.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-start p-3 border-2 border-primary-500 bg-primary-50 rounded-xl cursor-pointer transition">
                                    <input type="radio" name="recipientGroup" value="self" checked class="mt-1 mr-3 accent-primary-600">
                                    <div><p class="font-semibold text-sm text-gray-900">Send to Self (Test)</p><p class="text-xs text-gray-500">Send only to your own email address</p></div>
                                </label>
                                <label class="flex items-start p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="recipientGroup" value="staff" class="mt-1 mr-3 accent-primary-600">
                                    <div><p class="font-semibold text-sm text-gray-900">Admins & Moderators</p><p class="text-xs text-gray-500">Internal test send to all staff</p></div>
                                </label>
                                <label class="flex items-start p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="recipientGroup" value="all" class="mt-1 mr-3 accent-primary-600">
                                    <div><p class="font-semibold text-sm text-gray-900">All Members</p><p class="text-xs text-gray-500">Send to every approved member</p></div>
                                </label>
                                <label class="flex items-start p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="recipientGroup" value="all_subscribers" class="mt-1 mr-3 accent-primary-600">
                                    <div><p class="font-semibold text-sm text-gray-900">All Members & Subscribers</p><p class="text-xs text-gray-500">Send to every approved member AND public email subscribers</p></div>
                                </label>
                            </div>
                            <div class="bg-orange-50 border border-orange-100 rounded-xl p-3 flex items-start space-x-2">
                                <svg class="h-5 w-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <p class="text-xs text-orange-800">Always send a test email to yourself first. Once sent, newsletters cannot be recalled.</p>
                            </div>
                        </div>

                        <div id="pane-settings" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm mb-1">Newsletter settings</h3>
                                <p class="text-xs text-gray-500">Configure delivery & tracking options.</p>
                            </div>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Sender Name</label><input type="text" id="senderName" value="Slough SEND PCF" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Reply-To Email</label><input type="email" id="replyTo" value="hello@sloughsend.org.uk" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm"></div>
                                <div class="space-y-2 pt-2">
                                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer"><div><p class="text-sm font-medium text-gray-900">Track opens</p><p class="text-xs text-gray-500">See how many recipients open this email</p></div><input type="checkbox" id="trackOpens" checked class="accent-primary-600 w-4 h-4"></label>
                                    <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer"><div><p class="text-sm font-medium text-gray-900">Track link clicks</p><p class="text-xs text-gray-500">Monitor which links get clicked</p></div><input type="checkbox" id="trackClicks" checked class="accent-primary-600 w-4 h-4"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="floatingToolbar" class="hidden fixed z-[60] bg-gray-900 text-white rounded-lg shadow-xl flex items-center p-1 space-x-0.5">
        <button onclick="formatText('bold')" class="p-2 hover:bg-gray-700 rounded font-bold text-xs" title="Bold">B</button>
        <button onclick="formatText('italic')" class="p-2 hover:bg-gray-700 rounded italic text-xs" title="Italic">I</button>
        <button onclick="formatText('underline')" class="p-2 hover:bg-gray-700 rounded underline text-xs" title="Underline">U</button>
        <div class="w-px h-4 bg-gray-600 mx-1"></div>
        <button onclick="openLinkPopover()" class="p-2 hover:bg-gray-700 rounded" title="Add Link"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg></button>
    </div>

    <div id="linkPopover" class="hidden fixed z-[60] bg-white rounded-lg shadow-xl border border-gray-200 p-2 flex items-center space-x-2">
        <input type="url" id="linkUrlInput" placeholder="https://example.com" class="text-sm px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-primary-500">
        <button onclick="applyLink()" class="bg-primary-600 text-white text-xs px-3 py-1 rounded font-medium hover:bg-primary-700">Apply</button>
    </div>

    <div id="slashMenu" class="hidden fixed z-50 slash-menu">
        <div class="bg-white rounded-xl shadow-2xl border border-gray-100 w-72 max-h-96 overflow-y-auto custom-scroll">
            <div class="p-2"><input type="text" id="slashSearch" placeholder="Search blocks..." oninput="filterSlashMenu()" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none"></div>
            <div id="slashMenuItems" class="p-2 pt-0"></div>
        </div>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-gray-900">Add Image</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="space-y-4">
                <div id="uploadZone" class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:border-primary-400 transition cursor-pointer" onclick="document.getElementById('imageFileInput').click()">
                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p class="text-sm text-gray-500 font-medium">Click to upload from device</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                    <input type="file" id="imageFileInput" accept="image/*" class="hidden" onchange="handleFileUpload(event)">
                </div>
                <div id="uploadPreview" class="hidden">
                    <img id="uploadPreviewImg" src="" class="w-full h-32 object-cover rounded-lg mb-2" alt="Preview">
                    <button onclick="clearUpload()" class="text-xs text-red-500 hover:text-red-600">Remove image</button>
                </div>
                <div class="relative"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div><div class="relative flex justify-center"><span class="bg-white px-2 text-xs text-gray-400">OR PASTE URL</span></div></div>
                <input type="url" id="imageUrl" placeholder="https://example.com/image.jpg" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Alt text description (for accessibility)</label><input type="text" id="imageAlt" placeholder="Describe the image" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Image Link URL (Optional)</label><input type="url" id="imageLink" placeholder="https://..." class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm"></div>
                <div class="flex space-x-2 pt-2">
                    <button onclick="closeImageModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    <button onclick="insertImage()" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Add Image</button>
                </div>
            </div>
        </div>
    </div>

    <div id="embedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-gray-900">Add Embed</h3>
                <button onclick="closeEmbedModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="selectEmbedPlatform('youtube')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">▶️</div><p class="text-xs font-medium text-gray-700">YouTube</p></button>
                        <button onclick="selectEmbedPlatform('tiktok')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">🎵</div><p class="text-xs font-medium text-gray-700">TikTok</p></button>
                        <button onclick="selectEmbedPlatform('instagram')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">📸</div><p class="text-xs font-medium text-gray-700">Instagram</p></button>
                        <button onclick="selectEmbedPlatform('twitter')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">🐦</div><p class="text-xs font-medium text-gray-700">X / Twitter</p></button>
                        <button onclick="selectEmbedPlatform('facebook')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">👍</div><p class="text-xs font-medium text-gray-700">Facebook</p></button>
                        <button onclick="selectEmbedPlatform('custom')" class="embed-platform-btn p-3 border-2 border-gray-200 rounded-lg hover:border-primary-500 transition text-center"><div class="text-2xl mb-1">🔗</div><p class="text-xs font-medium text-gray-700">Custom</p></button>
                    </div>
                </div>
                <div id="embedUrlWrapper" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Embed URL</label>
                    <input type="url" id="embedUrl" placeholder="Paste link here..." class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                    <p class="text-xs text-gray-400 mt-1" id="embedHint">Paste a public post URL</p>
                </div>
                <div class="flex space-x-2 pt-2">
                    <button onclick="closeEmbedModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    <button onclick="insertEmbed()" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Add Embed</button>
                </div>
            </div>
        </div>
    </div>

    <div id="ctaModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-gray-900">Add Button</h3>
                <button onclick="closeCtaModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label><input type="text" id="ctaText" placeholder="Read More" class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label><input type="url" id="ctaUrl" placeholder="https://..." class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Style</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer"><input type="radio" name="ctaStyle" value="orange" checked class="sr-only peer"><div class="p-2 border-2 border-gray-200 rounded-lg peer-checked:border-orange-500 text-center text-xs font-medium text-white bg-orange-500">Orange</div></label>
                        <label class="cursor-pointer"><input type="radio" name="ctaStyle" value="teal" class="sr-only peer"><div class="p-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-xs font-medium text-white bg-primary-600">Primary</div></label>
                        <label class="cursor-pointer"><input type="radio" name="ctaStyle" value="outline" class="sr-only peer"><div class="p-2 border-2 border-gray-200 rounded-lg peer-checked:border-primary-500 text-center text-xs font-medium text-primary-700 border-primary-500 bg-white">Outline</div></label>
                    </div>
                </div>
                <div class="flex space-x-2 pt-2">
                    <button onclick="closeCtaModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    <button onclick="insertCta()" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Add Button</button>
                </div>
            </div>
        </div>
    </div>

    <div id="sendModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-gray-900">Send Newsletter</h3>
                <button onclick="closeSendModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="space-y-4">
                <div class="bg-primary-50 border border-primary-100 rounded-xl p-4"><p class="text-sm text-primary-900">You're about to send <strong id="sendModalTitle">this newsletter</strong> to <strong id="sendModalRecipients">all recipients</strong>.</p></div>
                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer"><input type="checkbox" id="confirmSend" class="mr-3 accent-primary-600 w-4 h-4"><span class="text-sm text-gray-700">I've reviewed the preview and I'm ready to send</span></label>
                <div class="flex space-x-2 pt-2">
                    <button onclick="closeSendModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    <button onclick="confirmSend()" id="confirmSendBtn" disabled class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center">
                        Send Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg flex items-center space-x-2">
        <span id="toastIcon" class="flex items-center">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </span>
        <span id="toastText" class="text-sm">Saved!</span>
    </div>
</div>

@push('scripts')
<script>
    const existingData = @json($this->existingDataArray);
    const uploadUrl = '{{ route("admin.newsletters.upload-image") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function getWire() {
        const rootEl = document.querySelector('.newsletter-editor-root');
        if (rootEl && window.Livewire) {
            const id = rootEl.getAttribute('wire:id');
            if (id) return window.Livewire.find(id);
        }
        return null;
    }

    let autoSaveTimeout;
    let saveTimeout;
    let blocks = [];
    let selectedBlockId = null;
    let selectedPreviewBlockId = null;
    let slashMenuContext = null;
    let pendingImageAfterId = null;
    let pendingEmbedAfterId = null;
    let pendingEmbedPlatform = null;
    let pendingCtaAfterId = null;
    let blockIdCounter = 0;
    let draggedItem = null;
    let currentImageBlockId = null;
    let uploadedImageUrl = null;
    let savedRange = null;

    let styleConfig = {
        primaryColor: '#141f52', accentColor: '#f97316', linkColor: '#141f52',
        textOnPrimary: '#ffffff', bodyTextColor: '#334155', headingColor: '#0f172a',
        mutedColor: '#64748b', background: '#ffffff', outerBackground: '#f1f5f9',
        borderColor: '#e2e8f0', blockquoteBg: '#e6e8f1',
        headingFont: 'Poppins', bodyFont: 'Open Sans',
        baseFontSize: 15, headingScale: 1.0, lineHeight: 1.6, letterSpacing: 0,
        contentWidth: 600, contentPadding: 32, blockSpacing: 16, borderRadius: 10,
        buttonRadius: 999, headerStyle: 'gradient', headerText: 'Slough SEND Parent Carer Forum',
        headerLogoText: 'S', headerLogoUrl: '/build/assets/images/logo/sspcf_logo.png',
        headerBgColor: '#141f52', headerGradientEnd: '#2b3a7a',
        headerTextColor: '#ffffff', headerPadding: 20,
        footerText: "Together, we are creating a stronger future for SEND families across Slough. 💙",
        footerOrgName: 'Slough SEND Parent Carer Forum', footerBgColor: '#0f172a',
        footerTextColor: '#94a3b8', footerLinkColor: '#6a78b5',
        showUnsubscribe: true, showSocial: false,
        footerWebsiteText: 'www.sloughsendpcf.org', footerWebsiteUrl: 'https://www.sloughsendpcf.org',
        footerEmail: 'admin@sloughsendpcf.org',
        footerFacebookText: 'Slough SEND Parent Carer Forum', footerFacebookUrl: 'https://www.facebook.com/',
        footerInstagramText: '@sloughsendpcf', footerInstagramUrl: 'https://www.instagram.com/sloughsendpcf',
        footerTagline: 'Every Voice Matters',
        footerSubTagline: 'Listening • Supporting • Representing • Influencing • Empowering',
        footerLocation: 'Slough SEND Parent Carer Forum, Slough, UK',
    };
    const DEFAULT_STYLE = JSON.parse(JSON.stringify(styleConfig));

    const PRESETS = {
        teal:   { primaryColor:'#141f52', accentColor:'#f97316', linkColor:'#141f52', headerBgColor:'#141f52', headerGradientEnd:'#2b3a7a', blockquoteBg:'#e6e8f1' },
        purple: { primaryColor:'#7c3aed', accentColor:'#f59e0b', linkColor:'#7c3aed', headerBgColor:'#7c3aed', headerGradientEnd:'#a78bfa', blockquoteBg:'#faf5ff' },
        blue:   { primaryColor:'#1d4ed8', accentColor:'#ef4444', linkColor:'#1d4ed8', headerBgColor:'#1d4ed8', headerGradientEnd:'#60a5fa', blockquoteBg:'#eff6ff' },
        rose:   { primaryColor:'#be123c', accentColor:'#0ea5e9', linkColor:'#be123c', headerBgColor:'#be123c', headerGradientEnd:'#fb7185', blockquoteBg:'#fff1f2' },
        forest: { primaryColor:'#166534', accentColor:'#ca8a04', linkColor:'#166534', headerBgColor:'#166534', headerGradientEnd:'#86efac', blockquoteBg:'#f0fdf4' },
        slate:  { primaryColor:'#334155', accentColor:'#f97316', linkColor:'#334155', headerBgColor:'#334155', headerGradientEnd:'#94a3b8', blockquoteBg:'#f8fafc' },
    };

    function setMode(mode) {
        const writeBtn = document.getElementById('mode-write');
        const styleBtn = document.getElementById('mode-style');
        const writePanel = document.getElementById('writePanel');
        const stylePanel = document.getElementById('stylePanel');
        if (mode === 'style') {
            writeBtn.classList.remove('bg-primary-600','text-white');
            writeBtn.classList.add('text-gray-500','hover:bg-gray-50');
            styleBtn.classList.add('bg-primary-600','text-white');
            styleBtn.classList.remove('text-gray-500','hover:bg-gray-50');
            writePanel.classList.add('hidden');
            stylePanel.classList.remove('hidden');
        } else {
            styleBtn.classList.remove('bg-primary-600','text-white');
            styleBtn.classList.add('text-gray-500','hover:bg-gray-50');
            writeBtn.classList.add('bg-primary-600','text-white');
            writeBtn.classList.remove('text-gray-500','hover:bg-gray-50');
            stylePanel.classList.add('hidden');
            writePanel.classList.remove('hidden');
            clearPreviewBlockSelection();
        }
    }

    function applyStyleToPreview() {
        const wrapper = document.getElementById('previewWrapper');
        if (!wrapper) return;
        const s = styleConfig;
        wrapper.style.setProperty('--primary-color', s.primaryColor);
        wrapper.style.setProperty('--accent-color', s.accentColor);
        wrapper.style.setProperty('--link-color', s.linkColor);
        wrapper.style.setProperty('--text-on-primary', s.textOnPrimary);
        wrapper.style.setProperty('--body-text-color', s.bodyTextColor);
        wrapper.style.setProperty('--heading-color', s.headingColor);
        wrapper.style.setProperty('--muted-color', s.mutedColor);
        wrapper.style.setProperty('--background', s.background);
        wrapper.style.setProperty('--border-color', s.borderColor);
        wrapper.style.setProperty('--blockquote-bg', s.blockquoteBg);
        wrapper.style.setProperty('--heading-font', `'${s.headingFont}', sans-serif`);
        wrapper.style.setProperty('--body-font', `'${s.bodyFont}', sans-serif`);
        wrapper.style.setProperty('--base-font-size', s.baseFontSize + 'px');
        wrapper.style.setProperty('--heading-scale', s.headingScale);
        wrapper.style.setProperty('--line-height', s.lineHeight);
        wrapper.style.setProperty('--content-width', s.contentWidth + 'px');
        wrapper.style.setProperty('--content-padding', s.contentPadding + 'px');
        wrapper.style.setProperty('--block-spacing', s.blockSpacing + 'px');
        wrapper.style.setProperty('--border-radius', s.borderRadius + 'px');
        wrapper.style.setProperty('--button-radius', s.buttonRadius + 'px');
        wrapper.style.setProperty('--header-bg-color', s.headerBgColor);
        wrapper.style.setProperty('--header-gradient-end', s.headerGradientEnd);
        wrapper.style.setProperty('--header-text-color', s.headerTextColor);
        wrapper.style.setProperty('--footer-bg-color', s.footerBgColor);
        wrapper.style.setProperty('--footer-text-color', s.footerTextColor);
        wrapper.style.setProperty('--footer-link-color', s.footerLinkColor);
        renderEmailHeader(wrapper);
        renderEmailFooter(wrapper);
        const contentHolder = wrapper.querySelector('.email-content');
        if (contentHolder) contentHolder.style.maxWidth = s.contentWidth + 'px';
        const innerContent = wrapper.querySelector('.email-content > div');
        if (innerContent) {
            innerContent.style.padding = s.contentPadding + 'px';
            innerContent.style.backgroundColor = s.background;
        }
        updatePreview();
        refreshBlockStylePanel();
    }

    function renderEmailHeader(wrapper) {
        const header = wrapper.querySelector('.email-header');
        if (!header) return;
        const s = styleConfig;
        header.className = 'email-header email-header-' + s.headerStyle;
        header.style.padding = (s.headerPadding || 20) + 'px';
        header.style.textAlign = 'center';
        header.style.color = s.headerTextColor;
        
        let logoHtml = '';
        if (s.headerLogoUrl) {
            logoHtml = `<img src="${s.headerLogoUrl}" alt="Logo" style="height: 50px; width: auto; display: block; margin: 0 auto 8px;">`;
        } else {
            let logoBg = s.headerStyle === 'minimal' || s.headerStyle === 'logo' ? s.primaryColor : 'rgba(255,255,255,0.2)';
            let logoColor = s.headerStyle === 'minimal' || s.headerStyle === 'logo' ? '#fff' : s.headerTextColor;
            logoHtml = `<div style="width:40px;height:40px;border-radius:9999px;background:${logoBg};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;margin:0 auto 8px;color:${logoColor}">${s.headerLogoText}</div>`;
        }
        
        header.innerHTML = logoHtml + `<p style="font-size:11px;text-transform:uppercase;letter-spacing:0.05em;opacity:0.9;margin:0">${s.headerText}</p>`;
    }

    function renderEmailFooter(wrapper) {
        const footer = wrapper.querySelector('.email-footer');
        if (!footer) return;
        const s = styleConfig;
        
        let contactHtml = `<div style="margin-bottom: 16px; text-align: center;">`;
        if (s.footerWebsiteUrl) contactHtml += `<p style="margin:0 0 4px;">🌐 Website: <a href="${s.footerWebsiteUrl}" target="_blank" style="color:${s.footerLinkColor}">${s.footerWebsiteText || s.footerWebsiteUrl}</a></p>`;
        if (s.footerEmail) contactHtml += `<p style="margin:0 0 4px;">📧 Email: <a href="mailto:${s.footerEmail}" style="color:${s.footerLinkColor}">${s.footerEmail}</a></p>`;
        if (s.footerFacebookUrl) contactHtml += `<p style="margin:0 0 4px;">📘 Facebook: <a href="${s.footerFacebookUrl}" target="_blank" style="color:${s.footerLinkColor}">${s.footerFacebookText || 'Facebook'}</a></p>`;
        if (s.footerInstagramUrl) contactHtml += `<p style="margin:0 0 4px;">📸 Instagram: <a href="${s.footerInstagramUrl}" target="_blank" style="color:${s.footerLinkColor}">${s.footerInstagramText || 'Instagram'}</a></p>`;
        contactHtml += `</div>`;
        
        let taglineHtml = '';
        if (s.footerTagline) taglineHtml += `<p style="margin:0 0 4px; font-weight:600; font-size:15px; color:${s.footerLinkColor};">${s.footerTagline}</p>`;
        if (s.footerSubTagline) taglineHtml += `<p style="margin:0 0 12px; font-size:12px; opacity:0.8;">${s.footerSubTagline}</p>`;
        if (s.footerText) taglineHtml += `<p style="margin:0 0 20px; font-size:13px;">${s.footerText}</p>`;
        
        let unsubHtml = '';
        if (s.showUnsubscribe) {
            unsubHtml = `<p style="margin:0 0 8px; font-size:12px;"><a href="#" target="_blank" style="color:${s.footerLinkColor}">Unsubscribe</a> | <a href="#" target="_blank" style="color:${s.footerLinkColor}">Manage subscription</a></p>`;
        }
        
        let locationHtml = '';
        if (s.footerLocation) locationHtml = `<p style="margin:0; font-size:11px; opacity:0.6;">${s.footerLocation}</p>`;
        
        footer.innerHTML = `
            <p style="margin:0 0 12px; font-size:16px; font-weight:700; color:${s.footerLinkColor};">Contact Us</p>
            <p style="margin:0 0 12px; font-weight:600;">${s.footerOrgName}</p>
            ${contactHtml}
            ${taglineHtml}
            ${unsubHtml}
            ${locationHtml}
        `;
        
        footer.querySelectorAll('a').forEach(a => a.setAttribute('target', '_blank'));
    }

    function bindStyleControls() {
        document.querySelectorAll('[data-style-key]').forEach(input => {
            const key = input.dataset.styleKey;
            const event = input.type === 'range' ? 'input' : input.type === 'checkbox' ? 'change' : input.type === 'radio' ? 'change' : 'input';
            input.addEventListener(event, () => {
                let val;
                if (input.type === 'checkbox') val = input.checked;
                else if (input.type === 'range') val = parseFloat(input.value);
                else if (input.type === 'radio') { if (!input.checked) return; val = input.value; }
                else val = input.value;
                styleConfig[key] = val;
                if (key === 'baseFontSize') document.getElementById('fontSizeLabel').textContent = val;
                if (key === 'headingScale') document.getElementById('headingScaleLabel').textContent = val.toFixed(2);
                if (key === 'lineHeight') document.getElementById('lineHeightLabel').textContent = val.toFixed(1);
                if (key === 'contentWidth') document.getElementById('widthLabel') && (document.getElementById('widthLabel').textContent = val);
                if (key === 'contentPadding') document.getElementById('paddingLabel').textContent = val;
                if (key === 'blockSpacing') document.getElementById('blockSpacingLabel').textContent = val;
                if (key === 'borderRadius') document.getElementById('radiusLabel').textContent = val;
                applyStyleToPreview();
                markUnsaved();
            });
        });
    }

    function bindBlockStyleControls() {
        const controls = [
            { id: 'blockBgColor', event: 'input' },
            { id: 'blockAlign', event: 'change' },
            { id: 'blockTextColor', event: 'input' },
            { id: 'blockFont', event: 'change' },
            { id: 'blockPadding', event: 'input' },
            { id: 'buttonColor', event: 'input' },
            { id: 'buttonSize', event: 'change' },
        ];
        controls.forEach(({ id, event }) => {
            const el = document.getElementById(id);
            if (el) el.addEventListener(event, updatePreviewBlockStyle);
        });
    }

    function applyPreset(name) {
        const p = PRESETS[name];
        if (!p) return;
        Object.assign(styleConfig, p);
        Object.entries(p).forEach(([k,v]) => { const el = document.querySelector(`[data-style-key="${k}"]`); if (el) el.value = v; });
        applyStyleToPreview();
        markUnsaved();
        showToast(`Applied ${name} theme`);
    }

    function applyCustomTheme() {
        const hex = document.getElementById('customThemeColor').value;
        function shadeColor(color, percent) {
            let R = parseInt(color.substring(1,3),16), G = parseInt(color.substring(3,5),16), B = parseInt(color.substring(5,7),16);
            R = Math.min(255, Math.max(0, Math.floor(R * (100 + percent) / 100)));
            G = Math.min(255, Math.max(0, Math.floor(G * (100 + percent) / 100)));
            B = Math.min(255, Math.max(0, Math.floor(B * (100 + percent) / 100)));
            return `#${R.toString(16).padStart(2,'0')}${G.toString(16).padStart(2,'0')}${B.toString(16).padStart(2,'0')}`;
        }
        const p = { primaryColor: hex, linkColor: hex, headerBgColor: hex, headerGradientEnd: shadeColor(hex, 30), blockquoteBg: shadeColor(hex, 85) };
        Object.assign(styleConfig, p);
        Object.entries(p).forEach(([k,v]) => { const el = document.querySelector(`[data-style-key="${k}"]`); if (el) el.value = v; });
        applyStyleToPreview();
        markUnsaved();
        showToast('Applied custom theme');
    }

    function resetStyle() {
        if (!confirm('Reset all styling to defaults?')) return;
        Object.assign(styleConfig, DEFAULT_STYLE);
        Object.keys(DEFAULT_STYLE).forEach(k => { const el = document.querySelector(`[data-style-key="${k}"]`); if (el) { if (el.type === 'checkbox') el.checked = styleConfig[k]; else el.value = styleConfig[k]; } });
        applyStyleToPreview();
        markUnsaved();
    }

    function linkifyHtml(html) {
        if (!html) return '';
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const walker = document.createTreeWalker(temp, NodeFilter.SHOW_TEXT, null, false);
        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);
        nodes.forEach(node => {
            if (!node.parentNode || node.parentElement.tagName === 'A') return;
            const text = node.nodeValue;
            const regex = /((https?:\/\/|www\.)[^\s<]+)/gi;
            if (!regex.test(text)) return;
            regex.lastIndex = 0;
            const frag = document.createDocumentFragment();
            let last = 0, match;
            while ((match = regex.exec(text)) !== null) {
                frag.appendChild(document.createTextNode(text.slice(last, match.index)));
                let url = match[0];
                let a = document.createElement('a');
                a.href = url.startsWith('www.') ? 'http://' + url : url;
                a.target = '_blank';
                a.textContent = url;
                frag.appendChild(a);
                last = match.index + url.length;
            }
            frag.appendChild(document.createTextNode(text.slice(last)));
            node.parentNode.replaceChild(frag, node);
        });
        temp.querySelectorAll('a').forEach(a => a.setAttribute('target', '_blank'));
        return temp.innerHTML;
    }

    const BLOCK_TYPES = [
        { id: 'paragraph',  label: 'Text',          icon: '📝', desc: 'Plain paragraph text', tag: 'p', placeholder: "Type something, or press '/' for commands..." },
        { id: 'h1',         label: 'Heading 1',     icon: '🔠', desc: 'Large section heading', tag: 'h1', placeholder: 'Heading 1' },
        { id: 'h2',         label: 'Heading 2',     icon: '🔤', desc: 'Medium section heading', tag: 'h2', placeholder: 'Heading 2' },
        { id: 'h3',         label: 'Heading 3',     icon: '🔡', desc: 'Smaller sub-heading', tag: 'h3', placeholder: 'Heading 3' },
        { id: 'h4',         label: 'Heading 4',     icon: 'ᴀ',  desc: 'Minor sub-heading', tag: 'h4', placeholder: 'Heading 4' },
        { id: 'h5',         label: 'Heading 5',     icon: 'ᴀ',  desc: 'Small sub-heading', tag: 'h5', placeholder: 'Heading 5' },
        { id: 'h6',         label: 'Heading 6',     icon: 'ᴀ',  desc: 'Smallest heading', tag: 'h6', placeholder: 'Heading 6' },
        { id: 'bulletList', label: 'Bullet List',   icon: '•',  desc: 'Unordered list', tag: 'ul', placeholder: 'List item' },
        { id: 'numberList', label: 'Numbered List', icon: '1.', desc: 'Ordered list', tag: 'ol', placeholder: 'List item' },
        { id: 'quote',      label: 'Quote',         icon: '❝',  desc: 'Highlighted quotation', tag: 'blockquote', placeholder: 'Quote text...' },
        { id: 'image',      label: 'Image',         icon: '🖼️', desc: 'Upload or link a photo', tag: 'img', placeholder: '' },
        { id: 'divider',    label: 'Divider',       icon: '—',  desc: 'Horizontal line', tag: 'hr', placeholder: '' },
        { id: 'spacer',     label: 'Spacer',        icon: '␣',  desc: 'Vertical whitespace', tag: 'div', placeholder: '' },
        { id: 'cta',        label: 'Button',        icon: '🔘', desc: 'Call-to-action button', tag: 'button', placeholder: '' },
        { id: 'embed',      label: 'Embed',         icon: '🔗', desc: 'YouTube, TikTok, Instagram, etc.', tag: 'iframe', placeholder: '' }
    ];

    function addBlock(type, afterId = null, content = '') {
        const blockType = BLOCK_TYPES.find(b => b.id === type);
        if (!blockType) return;
        const id = 'block-' + (++blockIdCounter);
        const block = { id, type, content, meta: {} };
        if (type === 'bulletList' || type === 'numberList') block.content = '<li>List item</li>';
        if (type === 'divider') block.content = '<hr>';
        if (type === 'spacer') block.content = '';
        if (afterId) { const idx = blocks.findIndex(b => b.id === afterId); blocks.splice(idx + 1, 0, block); }
        else { blocks.push(block); }
        renderBlocks();
        updatePreview();
        updateBlockCount();
        setTimeout(() => {
            const el = document.querySelector(`[data-block-id="${id}"] [contenteditable]`);
            if (el) {
                el.focus();
                const range = document.createRange();
                range.selectNodeContents(el);
                range.collapse(false);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
            selectBlock(id);
        }, 50);
        return id;
    }

    function renderBlocks() {
        const editor = document.getElementById('blockEditor');
        editor.innerHTML = '';
        if (blocks.length === 0) {
            editor.innerHTML = `<div class="text-center py-12 text-gray-400"><svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg><p class="text-sm">No blocks yet. Click <strong>"Add Block"</strong> above or the button below to get started.</p></div>`;
            return;
        }
        blocks.forEach((block, idx) => {
            if (idx > 0) {
                const spacer = document.createElement('div');
                spacer.className = 'add-block-wrapper relative h-3 flex items-center justify-center';
                spacer.innerHTML = `<button onclick="openSlashMenuAt(null, '${blocks[idx-1].id}')" class="add-block-btn w-6 h-6 rounded-full bg-gray-100 hover:bg-primary-500 hover:text-white text-gray-400 flex items-center justify-center transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>`;
                editor.appendChild(spacer);
            }
            const blockEl = createBlockElement(block);
            editor.appendChild(blockEl);
        });
    }

    function createBlockElement(block) {
        const wrapper = document.createElement('div');
        wrapper.className = 'editor-block group';
        wrapper.dataset.blockId = block.id;
        wrapper.onclick = (e) => { if (e.target.closest('.block-controls')) return; selectBlock(block.id); };
        const controls = document.createElement('div');
        controls.className = 'block-controls absolute -top-3 right-3 flex bg-white border border-gray-200 rounded-lg shadow-md p-1 space-x-1 z-10';

        let editButton = '';
        const editIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>';

        if (block.type === 'image' && block.meta.src) editButton = `<button onclick="openImageModal('${block.id}')" title="Edit Image" class="p-1 text-gray-400 hover:text-primary-600 transition">${editIcon}</button>`;
        else if (block.type === 'cta' && block.meta.text) editButton = `<button onclick="openCtaModal('${block.id}')" title="Edit Button" class="p-1 text-gray-400 hover:text-primary-600 transition">${editIcon}</button>`;
        else if (block.type === 'embed' && block.meta.url) editButton = `<button onclick="openEmbedModal('${block.id}')" title="Edit Embed" class="p-1 text-gray-400 hover:text-primary-600 transition">${editIcon}</button>`;

        controls.innerHTML = `
            <button class="drag-handle p-1 text-gray-400 hover:text-primary-600 transition cursor-move" title="Drag to reorder"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg></button>
            ${editButton}
            <button onclick="openSlashMenuAt(event, '${block.id}')" title="Add block below" class="p-1 text-gray-400 hover:text-primary-600 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>
            <button onclick="duplicateBlock('${block.id}')" title="Duplicate" class="p-1 text-gray-400 hover:text-primary-600 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg></button>
            <button onclick="deleteBlock('${block.id}')" title="Delete" class="p-1 text-gray-400 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>`;

        const content = document.createElement('div');
        content.className = 'block-content';
        content.innerHTML = getBlockInnerHtml(block);
        wrapper.appendChild(controls);
        wrapper.appendChild(content);
        const dragHandle = controls.querySelector('.drag-handle');
        dragHandle.addEventListener('mousedown', () => { wrapper.draggable = true; });
        attachDragListeners(wrapper, block.id);
        setTimeout(() => {
            const editables = wrapper.querySelectorAll('[contenteditable]');
            editables.forEach(editable => {
                if (editable.classList.contains('caption-input')) {
                    editable.addEventListener('input', (e) => { const b = blocks.find(x => x.id === block.id); if (b) { b.meta.caption = e.target.innerHTML; updatePreview(); markUnsaved(); } });
                    editable.addEventListener('blur', (e) => {
                        const b = blocks.find(x => x.id === block.id);
                        if (!b) return;
                        const linkedHtml = linkifyHtml(editable.innerHTML);
                        if (linkedHtml !== editable.innerHTML) {
                            editable.innerHTML = linkedHtml;
                            b.meta.caption = linkedHtml;
                            updatePreview();
                            markUnsaved();
                        }
                    });
                } else {
                    editable.addEventListener('input', (e) => { const b = blocks.find(x => x.id === block.id); if (b) { b.content = e.target.innerHTML; updatePreview(); markUnsaved(); } });
                    editable.addEventListener('keydown', (e) => {
                        if (e.key === '/' && editable.textContent.trim() === '') { e.preventDefault(); const rect = editable.getBoundingClientRect(); openSlashMenuAt({ clientX: rect.left, clientY: rect.bottom + 4, preventDefault: () => {} }, block.id, true); }
                        if (e.key === 'Enter' && block.type !== 'paragraph' && !e.shiftKey && block.type !== 'bulletList' && block.type !== 'numberList') { e.preventDefault(); addBlock('paragraph', block.id); }
                        if (e.key === 'Backspace' && editable.textContent.trim() === '') { e.preventDefault(); deleteBlock(block.id); }
                    });
                    editable.addEventListener('blur', (e) => {
                        const b = blocks.find(x => x.id === block.id);
                        if (!b) return;
                        const linkedHtml = linkifyHtml(editable.innerHTML);
                        if (linkedHtml !== editable.innerHTML) {
                            editable.innerHTML = linkedHtml;
                            b.content = linkedHtml;
                            updatePreview();
                            markUnsaved();
                        }
                    });
                }
            });
        }, 0);
        return wrapper;
    }

    function attachDragListeners(el, blockId) {
        el.addEventListener('dragstart', (e) => { draggedItem = blockId; setTimeout(() => el.classList.add('dragging'), 0); });
        el.addEventListener('dragend', () => { el.classList.remove('dragging'); el.draggable = false; draggedItem = null; document.querySelectorAll('.editor-block').forEach(b => { b.classList.remove('drag-over-top', 'drag-over-bottom'); }); });
        el.addEventListener('dragover', (e) => { e.preventDefault(); if (!draggedItem || draggedItem === blockId) return; const rect = el.getBoundingClientRect(); const midpoint = rect.top + rect.height / 2; document.querySelectorAll('.editor-block').forEach(b => { if (b !== el) b.classList.remove('drag-over-top', 'drag-over-bottom'); }); if (e.clientY < midpoint) { el.classList.add('drag-over-top'); el.classList.remove('drag-over-bottom'); } else { el.classList.add('drag-over-bottom'); el.classList.remove('drag-over-top'); } });
        el.addEventListener('dragleave', (e) => { if (e.target === el) el.classList.remove('drag-over-top', 'drag-over-bottom'); });
        el.addEventListener('drop', (e) => { e.preventDefault(); const rect = el.getBoundingClientRect(); const midpoint = rect.top + rect.height / 2; const insertBefore = e.clientY < midpoint; el.classList.remove('drag-over-top', 'drag-over-bottom'); document.querySelectorAll('.editor-block').forEach(b => b.classList.remove('drag-over-top', 'drag-over-bottom')); if (draggedItem && draggedItem !== blockId) reorderBlocks(draggedItem, blockId, insertBefore); });
    }

    function reorderBlocks(draggedId, targetId, insertBefore) {
        const draggedIdx = blocks.findIndex(b => b.id === draggedId);
        let targetIdx = blocks.findIndex(b => b.id === targetId);
        if (draggedIdx === -1 || targetIdx === -1) return;
        const [draggedBlock] = blocks.splice(draggedIdx, 1);
        targetIdx = blocks.findIndex(b => b.id === targetId);
        if (insertBefore) blocks.splice(targetIdx, 0, draggedBlock); else blocks.splice(targetIdx + 1, 0, draggedBlock);
        renderBlocks(); updatePreview(); markUnsaved();
    }

    function getBlockInnerHtml(block) {
        const bt = BLOCK_TYPES.find(b => b.id === block.type);
        if (!bt) return '';
        switch (block.type) {
            case 'paragraph':  return `<p contenteditable="true" data-placeholder="${bt.placeholder}" class="text-base text-gray-800 leading-relaxed py-1 focus:outline-none border-none">${block.content || ''}</p>`;
            case 'h1': return `<h1 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-3xl font-bold text-gray-900 py-1 focus:outline-none border-none">${block.content || ''}</h1>`;
            case 'h2': return `<h2 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-2xl font-bold text-gray-900 py-1 focus:outline-none border-none">${block.content || ''}</h2>`;
            case 'h3': return `<h3 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-xl font-semibold text-gray-900 py-1 focus:outline-none border-none">${block.content || ''}</h3>`;
            case 'h4': return `<h4 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-lg font-semibold text-gray-900 py-1 focus:outline-none border-none">${block.content || ''}</h4>`;
            case 'h5': return `<h5 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-base font-semibold text-gray-900 py-1 focus:outline-none border-none">${block.content || ''}</h5>`;
            case 'h6': return `<h6 contenteditable="true" data-placeholder="${bt.placeholder}" class="text-sm font-semibold text-gray-500 uppercase tracking-wide py-1 focus:outline-none border-none">${block.content || ''}</h6>`;
            case 'bulletList': return `<ul contenteditable="true" data-placeholder="${bt.placeholder}" class="list-disc pl-6 text-gray-800 py-1 focus:outline-none border-none">${block.content || '<li>List item</li>'}</ul>`;
            case 'numberList': return `<ol contenteditable="true" data-placeholder="${bt.placeholder}" class="list-decimal pl-6 text-gray-800 py-1 focus:outline-none border-none">${block.content || '<li>List item</li>'}</ol>`;
            case 'quote': return `<blockquote contenteditable="true" data-placeholder="${bt.placeholder}" class="border-l-4 border-primary-500 bg-primary-50 pl-4 pr-3 py-2 italic text-gray-700 rounded-r-lg my-1 focus:outline-none">${block.content || ''}</blockquote>`;
            case 'divider': return `<div class="py-2"><hr class="border-gray-200"></div>`;
            case 'spacer': return `<div class="h-12 flex items-center justify-center text-xs text-gray-300">— spacer —</div>`;
            case 'image':
                if (block.meta.src) {
                    const imgTag = `<img src="${block.meta.src}" alt="${block.meta.alt || ''}" class="rounded-lg w-full">`;
                    const imgContent = block.meta.link ? `<a href="${block.meta.link}" target="_blank" onclick="event.preventDefault()" style="display: block; cursor: pointer;">${imgTag}</a>` : imgTag;
                    return `<figure class="my-2">${imgContent}<figcaption contenteditable="true" data-placeholder="Write a caption..." class="caption-input text-center text-gray-500 text-sm mt-2 focus:outline-none border-none">${block.meta.caption || ''}</figcaption></figure>`;
                }
                return `<button onclick="openImageModal('${block.id}')" class="w-full p-8 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 hover:border-primary-400 hover:text-primary-600 transition flex flex-col items-center"><svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span class="text-sm">Click to upload image</span></button>`;
            case 'cta':
                if (block.meta.text && block.meta.url) { const style = block.meta.style || 'orange'; const cls = style === 'orange' ? 'bg-orange-500 text-white hover:bg-orange-600' : style === 'teal' ? 'bg-primary-600 text-white hover:bg-primary-700' : 'border-2 border-primary-600 text-primary-700 hover:bg-primary-50'; return `<div class="my-2"><a href="${block.meta.url}" target="_blank" onclick="event.preventDefault()" class="inline-block px-6 py-2.5 rounded-full font-medium text-sm ${cls} transition">${block.meta.text}</a></div>`; }
                return `<button onclick="openCtaModal('${block.id}')" class="w-full p-6 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 hover:border-primary-400 hover:text-primary-600 transition flex items-center justify-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" /></svg><span class="text-sm">Add a button (CTA)</span></button>`;
            case 'embed':
                if (block.meta.url) {
                    let embedContentHtml = '';
                    if (block.meta.platform === 'custom') {
                        embedContentHtml = `<div class="my-2 p-4 border border-gray-200 rounded-lg flex items-center justify-between bg-gray-50 relative"><div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">🔗</div><div><p class="text-sm font-medium text-gray-900">External Link</p><p class="text-xs text-gray-500 truncate max-w-xs">${block.meta.originalUrl || block.meta.url}</p></div></div><a href="${block.meta.originalUrl || block.meta.url}" target="_blank" class="text-xs font-medium text-primary-600 hover:text-primary-700">Open ↗</a></div>`;
                    } else {
                        embedContentHtml = `<div class="my-2 relative"><iframe src="${block.meta.url}" class="w-full rounded-lg" style="min-height: 280px; border: none;" loading="lazy"></iframe><button onclick="deleteBlock('${block.id}')" class="absolute top-2 right-2 bg-white/90 backdrop-blur p-1 rounded text-gray-500 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button></div>`;
                    }
                    return `<figure class="my-2">${embedContentHtml}<figcaption contenteditable="true" data-placeholder="Write a caption..." class="caption-input text-center text-gray-500 text-sm mt-2 focus:outline-none border-none">${block.meta.caption || ''}</figcaption></figure>`;
                }
                return `<button onclick="openEmbedModal('${block.id}')" class="w-full p-6 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 hover:border-primary-400 hover:text-primary-600 transition flex items-center justify-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg><span class="text-sm">Add an embed</span></button>`;
            default: return '';
        }
    }

    function selectBlock(id) { selectedBlockId = id; document.querySelectorAll('.editor-block').forEach(el => el.classList.remove('selected')); const el = document.querySelector(`[data-block-id="${id}"]`); if (el) el.classList.add('selected'); }
    function deleteBlock(id) { blocks = blocks.filter(b => b.id !== id); renderBlocks(); updatePreview(); updateBlockCount(); markUnsaved(); }
    function duplicateBlock(id) { const idx = blocks.findIndex(b => b.id === id); if (idx === -1) return; const copy = JSON.parse(JSON.stringify(blocks[idx])); copy.id = 'block-' + (++blockIdCounter); blocks.splice(idx + 1, 0, copy); renderBlocks(); updatePreview(); markUnsaved(); }
    function updateBlockCount() { document.getElementById('blockCount').textContent = `${blocks.length} block${blocks.length !== 1 ? 's' : ''}`; }

    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        const toolbar = document.getElementById('floatingToolbar');
        const linkPopover = document.getElementById('linkPopover');
        if (!linkPopover.classList.contains('hidden')) return;
        if (selection.isCollapsed) { toolbar.classList.add('hidden'); return; }
        const editor = document.getElementById('blockEditor');
        if (editor.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            toolbar.classList.remove('hidden');
            toolbar.style.top = (rect.top - 45) + 'px';
            toolbar.style.left = (rect.left + rect.width / 2 - 60) + 'px';
        } else { toolbar.classList.add('hidden'); }
    });

    function formatText(command, value = null) {
        document.execCommand(command, false, value);
        document.getElementById('floatingToolbar').classList.add('hidden');
        const selection = window.getSelection();
        if (selection.anchorNode) {
            const blockEl = selection.anchorNode.parentElement.closest('.editor-block');
            if (blockEl) { const id = blockEl.dataset.blockId; const b = blocks.find(x => x.id === id); const editable = blockEl.querySelector('[contenteditable]'); if (b && editable && !editable.classList.contains('image-caption-input')) { b.content = editable.innerHTML; updatePreview(); markUnsaved(); } }
        }
    }

    function openLinkPopover() {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) savedRange = selection.getRangeAt(0);
        const toolbar = document.getElementById('floatingToolbar');
        const rect = toolbar.getBoundingClientRect();
        const popover = document.getElementById('linkPopover');
        popover.style.top = (rect.bottom + 8) + 'px';
        popover.style.left = (rect.left + rect.width / 2 - 100) + 'px';
        popover.classList.remove('hidden');
        setTimeout(() => document.getElementById('linkUrlInput').focus(), 50);
    }

    function applyLink() {
        let url = document.getElementById('linkUrlInput').value.trim();
        if (!url) return;
        if (!/^https?:\/\//i.test(url)) url = 'http://' + url;
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedRange);
        document.execCommand('createLink', false, url);
        const blockEl = savedRange.startContainer.parentElement.closest('.editor-block');
        if (blockEl) {
            const links = blockEl.querySelectorAll('a[href="' + url + '"]');
            links.forEach(l => l.setAttribute('target', '_blank'));
            const id = blockEl.dataset.blockId;
            const b = blocks.find(x => x.id === id);
            const editable = blockEl.querySelector('[contenteditable]');
            if (b && editable && !editable.classList.contains('image-caption-input')) {
                b.content = editable.innerHTML;
                updatePreview();
                markUnsaved();
            }
        }
        document.getElementById('linkPopover').classList.add('hidden');
        document.getElementById('linkUrlInput').value = '';
    }

    document.addEventListener('click', (e) => { const popover = document.getElementById('linkPopover'); if (!popover.classList.contains('hidden') && !e.target.closest('#linkPopover') && !e.target.closest('#floatingToolbar')) popover.classList.add('hidden'); });

    function openSlashMenu(btn) { openSlashMenuAt(null, null); }
    function openSlashMenuAt(e, afterId, replaceCurrent = false) {
        if (e && e.preventDefault) e.preventDefault();
        slashMenuContext = { afterId, replaceCurrent };
        const menu = document.getElementById('slashMenu');
        const search = document.getElementById('slashSearch');
        let x = 100, y = 100;
        if (e && e.clientX !== undefined) { x = e.clientX; y = e.clientY; } else if (e && e.target) { const rect = e.target.getBoundingClientRect ? e.target.getBoundingClientRect() : null; if (rect) { x = rect.left; y = rect.bottom + 4; } } else { const btn = document.querySelector(`[data-block-id="${afterId}"]`); if (btn) { const rect = btn.getBoundingClientRect(); x = rect.left + 40; y = rect.bottom + 4; } }
        menu.style.left = Math.min(x, window.innerWidth - 300) + 'px';
        menu.style.top = Math.min(y, window.innerHeight - 400) + 'px';
        menu.classList.remove('hidden');
        search.value = '';
        setTimeout(() => search.focus(), 50);
        renderSlashMenuItems('');
    }

    function renderSlashMenuItems(filter) {
        const container = document.getElementById('slashMenuItems');
        const filtered = BLOCK_TYPES.filter(b => !filter || b.label.toLowerCase().includes(filter.toLowerCase()) || b.desc.toLowerCase().includes(filter.toLowerCase()));
        if (filtered.length === 0) { container.innerHTML = `<p class="text-sm text-gray-400 text-center py-4">No blocks match "${filter}"</p>`; return; }
        container.innerHTML = filtered.map(b => `<button onclick="selectSlashItem('${b.id}')" class="w-full flex items-center space-x-3 p-2.5 rounded-lg hover:bg-primary-50 transition text-left"><div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-lg shrink-0">${b.icon}</div><div><p class="text-sm font-medium text-gray-900">${b.label}</p><p class="text-xs text-gray-500">${b.desc}</p></div></button>`).join('');
    }

    function filterSlashMenu() { renderSlashMenuItems(document.getElementById('slashSearch').value); }
    function selectSlashItem(type) {
        closeSlashMenu();
        const ctx = slashMenuContext;
        if (ctx && ctx.replaceCurrent && ctx.afterId) { const idx = blocks.findIndex(b => b.id === ctx.afterId); if (idx !== -1) { blocks[idx].type = type; blocks[idx].content = (type === 'bulletList' || type === 'numberList') ? '<li>List item</li>' : ''; blocks[idx].meta = {}; renderBlocks(); updatePreview(); setTimeout(() => selectBlock(ctx.afterId), 50); return; } }
        addBlock(type, ctx ? ctx.afterId : null);
    }
    function closeSlashMenu() { document.getElementById('slashMenu').classList.add('hidden'); }
    document.addEventListener('click', (e) => { if (!e.target.closest('#slashMenu') && !e.target.closest('[onclick*="openSlashMenu"]')) closeSlashMenu(); });

    function openImageModal(blockId) {
        currentImageBlockId = blockId;
        document.getElementById('imageModal').classList.remove('hidden');
        const b = blocks.find(x => x.id === blockId);
        if (b && b.meta.src) {
            if (b.meta.src.startsWith('http')) { document.getElementById('imageUrl').value = b.meta.src; clearUpload(); }
            else { uploadedImageUrl = b.meta.src; document.getElementById('uploadPreviewImg').src = b.meta.src; document.getElementById('uploadPreview').classList.remove('hidden'); document.getElementById('uploadZone').classList.add('hidden'); document.getElementById('imageUrl').value = ''; }
            document.getElementById('imageAlt').value = b.meta.alt || '';
            document.getElementById('imageLink').value = b.meta.link || '';
        } else {
            document.getElementById('imageUrl').value = '';
            document.getElementById('imageAlt').value = '';
            document.getElementById('imageLink').value = '';
            clearUpload();
        }
    }
    function closeImageModal() { document.getElementById('imageModal').classList.add('hidden'); currentImageBlockId = null; uploadedImageUrl = null; }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) { showToast('File size exceeds 5MB', 'error'); return; }
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', csrfToken);
        fetch(uploadUrl, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                uploadedImageUrl = data.url;
                document.getElementById('uploadPreviewImg').src = data.url;
                document.getElementById('uploadPreview').classList.remove('hidden');
                document.getElementById('uploadZone').classList.add('hidden');
            })
            .catch(error => { showToast('Upload failed. Please try again.', 'error'); console.error('Upload error:', error); });
    }

    function handleHeaderLogoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        if (file.size > 5 * 1024 * 1024) { 
            showToast('File size exceeds 5MB', 'error'); 
            return; 
        }
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', csrfToken);
        
        showToast('Uploading logo...');

        fetch(uploadUrl, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                const input = document.getElementById('headerLogoUrlInput');
                input.value = data.url;
                input.dispatchEvent(new Event('input'));
                showToast('Logo uploaded successfully');
            } else {
                showToast('Upload failed.', 'error');
            }
        })
        .catch(error => { 
            showToast('Upload failed. Please try again.', 'error'); 
            console.error('Logo upload error:', error); 
        });
    }

    function clearUpload() { uploadedImageUrl = null; document.getElementById('imageFileInput').value = ''; document.getElementById('uploadPreview').classList.add('hidden'); document.getElementById('uploadZone').classList.remove('hidden'); }

    function insertImage() {
        const url = document.getElementById('imageUrl').value.trim();
        const alt = document.getElementById('imageAlt').value.trim();
        const link = document.getElementById('imageLink').value.trim();
        const src = uploadedImageUrl || url;
        if (!src) { showToast('Please upload an image or enter a URL', 'error'); return; }
        if (currentImageBlockId) { const b = blocks.find(x => x.id === currentImageBlockId); if (b) { b.meta = { src, alt, link, caption: b.meta.caption || '' }; b.content = ''; renderBlocks(); } }
        else { const id = addBlock('image', pendingImageAfterId); const b = blocks.find(x => x.id === id); if (b) { b.meta = { src, alt, link, caption: '' }; renderBlocks(); } }
        updatePreview(); closeImageModal(); markUnsaved();
    }

    let currentEmbedBlockId = null;

    function openEmbedModal(blockId) {
        currentEmbedBlockId = blockId;
        document.getElementById('embedModal').classList.remove('hidden');
        const b = blocks.find(x => x.id === blockId);
        if (b && b.meta.url) {
            document.getElementById('embedUrlWrapper').classList.remove('hidden');
            document.getElementById('embedUrl').value = b.meta.originalUrl || b.meta.url || '';
            pendingEmbedPlatform = b.meta.platform || null;
        } else {
            document.getElementById('embedUrlWrapper').classList.add('hidden');
            document.getElementById('embedUrl').value = '';
            pendingEmbedPlatform = null;
        }
        document.querySelectorAll('.embed-platform-btn').forEach(btn => btn.classList.remove('border-primary-500', 'bg-primary-50'));
    }
    function closeEmbedModal() { document.getElementById('embedModal').classList.add('hidden'); currentEmbedBlockId = null; pendingEmbedPlatform = null; }
    function selectEmbedPlatform(platform) {
        pendingEmbedPlatform = platform;
        document.querySelectorAll('.embed-platform-btn').forEach(b => { b.classList.remove('border-primary-500', 'bg-primary-50'); b.classList.add('border-gray-200'); });
        event.target.closest('.embed-platform-btn').classList.add('border-primary-500', 'bg-primary-50');
        document.getElementById('embedUrlWrapper').classList.remove('hidden');
        const hints = { youtube: 'e.g. https://www.youtube.com/watch?v=...', tiktok: 'e.g. https://www.tiktok.com/@user/video/...', instagram: 'e.g. https://www.instagram.com/p/...', twitter: 'e.g. https://x.com/user/status/...', facebook: 'e.g. https://www.facebook.com/...', custom: 'Any embeddable URL or iframe src' };
        document.getElementById('embedHint').textContent = hints[platform] || '';
    }
    function insertEmbed() {
        const url = document.getElementById('embedUrl').value.trim();
        if (!url) { showToast('Please enter a URL', 'error'); return; }
        if (!pendingEmbedPlatform) { showToast('Please choose a platform', 'error'); return; }
        const embedUrl = convertEmbedUrl(pendingEmbedPlatform, url);
        if (!embedUrl) { showToast('Could not parse this URL', 'error'); return; }
        if (currentEmbedBlockId) { const b = blocks.find(x => x.id === currentEmbedBlockId); if (b) { b.meta = { url: embedUrl, originalUrl: url, platform: pendingEmbedPlatform }; renderBlocks(); } }
        else { const id = addBlock('embed', pendingEmbedAfterId); const b = blocks.find(x => x.id === id); if (b) { b.meta = { url: embedUrl, originalUrl: url, platform: pendingEmbedPlatform }; renderBlocks(); } }
        updatePreview(); closeEmbedModal(); markUnsaved();
    }

    function convertEmbedUrl(platform, url) {
        try {
            if (platform === 'youtube') { const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/); return m ? `https://www.youtube.com/embed/${m[1]}` : null; }
            if (platform === 'tiktok') { const m = url.match(/video\/(\d+)/); return m ? `https://www.tiktok.com/embed/v2/${m[1]}` : url; }
            if (platform === 'instagram') { const m = url.match(/instagram\.com\/(?:p|reel)\/([\w-]+)/); return m ? `https://www.instagram.com/p/${m[1]}/embed` : null; }
            if (platform === 'twitter') { const m = url.match(/x\.com|twitter\.com\/.*\/status\/(\d+)/); return m ? `https://platform.twitter.com/embed/Tweet.html?id=${m[1]}` : url; }
            if (platform === 'facebook') return `https://www.facebook.com/plugins/post.php?href=${encodeURIComponent(url)}&show_text=true`;
            return url;
        } catch (err) { return url; }
    }

    let currentCtaBlockId = null;
    function openCtaModal(blockId) {
        currentCtaBlockId = blockId;
        document.getElementById('ctaModal').classList.remove('hidden');
        const b = blocks.find(x => x.id === blockId);
        if (b && b.meta.text) {
            document.getElementById('ctaText').value = b.meta.text || '';
            document.getElementById('ctaUrl').value = b.meta.url || '';
            const style = b.meta.style || 'orange';
            const styleInput = document.querySelector(`input[name="ctaStyle"][value="${style}"]`);
            if (styleInput) styleInput.checked = true;
        } else {
            document.getElementById('ctaText').value = '';
            document.getElementById('ctaUrl').value = '';
            document.querySelector('input[name="ctaStyle"][value="orange"]').checked = true;
        }
    }
    function closeCtaModal() { document.getElementById('ctaModal').classList.add('hidden'); currentCtaBlockId = null; }
    function insertCta() {
        const text = document.getElementById('ctaText').value.trim();
        const url = document.getElementById('ctaUrl').value.trim();
        const style = document.querySelector('input[name="ctaStyle"]:checked').value;
        if (!text || !url) { showToast('Please fill in both fields', 'error'); return; }
        if (currentCtaBlockId) { const b = blocks.find(x => x.id === currentCtaBlockId); if (b) { b.meta = { text, url, style }; renderBlocks(); } }
        else { const id = addBlock('cta', pendingCtaAfterId); const b = blocks.find(x => x.id === id); if (b) { b.meta = { text, url, style }; renderBlocks(); } }
        updatePreview(); closeCtaModal(); markUnsaved();
    }

    function updatePreview() {
        const title = document.getElementById('newsletterTitle').value || 'Your newsletter title';
        document.getElementById('previewTitle').textContent = title;
        const now = new Date();
        document.getElementById('previewDate').textContent = now.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
        const pt = document.getElementById('previewText').value;
        document.getElementById('previewCount').textContent = pt.length;
        const preview = document.getElementById('previewContent');
        if (blocks.length === 0) { preview.innerHTML = '<p class="text-gray-400 italic">Start adding blocks to see your newsletter come to life...</p>'; return; }
        preview.innerHTML = blocks.map(b => renderBlockForPreview(b)).join('');
        preview.querySelectorAll('a').forEach(a => a.setAttribute('target', '_blank'));
        bindPreviewBlockSelection();
        if (selectedPreviewBlockId) { const el = document.querySelector(`[data-preview-block-id="${selectedPreviewBlockId}"]`); if (el) el.classList.add('preview-block-selected'); }
    }

    function renderBlockForPreview(block) {
        const styles = block.meta.styles || {};
        const wrapperStyle = `background:${styles.bgColor || 'transparent'};padding:${styles.padding || '0px'};text-align:${styles.align || 'left'};`;
        let innerStyleStr = '';
        if (styles.textColor) innerStyleStr += `color:${styles.textColor};`;
        if (styles.font && styles.font !== 'inherit') innerStyleStr += `font-family:'${styles.font}', sans-serif;`;
        if (block.type === 'quote' && styles.bgColor) innerStyleStr += `background:${styles.bgColor};`;
        const styleAttrs = innerStyleStr ? `style="${innerStyleStr}"` : '';

        let inner = '';
        switch (block.type) {
            case 'paragraph':  inner = `<p ${styleAttrs}>${linkifyHtml(block.content || '')}</p>`; break;
            case 'h1': inner = `<h1 ${styleAttrs}>${linkifyHtml(block.content || '')}</h1>`; break;
            case 'h2': inner = `<h2 ${styleAttrs}>${linkifyHtml(block.content || '')}</h2>`; break;
            case 'h3': inner = `<h3 ${styleAttrs}>${linkifyHtml(block.content || '')}</h3>`; break;
            case 'h4': inner = `<h4 ${styleAttrs}>${linkifyHtml(block.content || '')}</h4>`; break;
            case 'h5': inner = `<h5 ${styleAttrs}>${linkifyHtml(block.content || '')}</h5>`; break;
            case 'h6': inner = `<h6 ${styleAttrs}>${linkifyHtml(block.content || '')}</h6>`; break;
            case 'bulletList':  inner = `<ul ${styleAttrs}>${linkifyHtml(block.content || '')}</ul>`; break;
            case 'numberList':  inner = `<ol ${styleAttrs}>${linkifyHtml(block.content || '')}</ol>`; break;
            case 'quote':       inner = `<blockquote ${styleAttrs}>${linkifyHtml(block.content || '')}</blockquote>`; break;
            case 'divider':     inner = `<hr>`; break;
            case 'spacer':      inner = `<div style="height: 32px;"></div>`; break;
            case 'image':
                if (!block.meta.src) return '';
                const imgTag = `<img src="${block.meta.src}" alt="${block.meta.alt || ''}">`;
                const imgContent = block.meta.link ? `<a href="${block.meta.link}" target="_blank" style="display: block; cursor: pointer;">${imgTag}</a>` : imgTag;
                inner = `<figure>${imgContent}${block.meta.caption ? `<figcaption style="font-size:12px;color:#64748b;text-align:center;margin-top:4px;">${linkifyHtml(block.meta.caption)}</figcaption>` : ''}</figure>`;
                break;
            case 'cta': {
                if (!block.meta.text || !block.meta.url) return '';
                let btnColor;
                if (styles.buttonColor) btnColor = styles.buttonColor;
                else if (block.meta.style === 'teal') btnColor = 'var(--primary-color)';
                else if (block.meta.style === 'outline') btnColor = 'var(--primary-color)';
                else btnColor = 'var(--accent-color)';

                const btnSize = styles.buttonSize || 'medium';
                const sizeStyles = {
                    small: 'padding:6px 16px;font-size:13px;',
                    medium: 'padding:10px 22px;font-size:15px;',
                    large: 'padding:14px 28px;font-size:17px;'
                };
                let btnStyleStr;
                if (block.meta.style === 'outline') {
                    btnStyleStr = `background:transparent;color:${btnColor};border:2px solid ${btnColor};${sizeStyles[btnSize]}`;
                } else {
                    btnStyleStr = `background:${btnColor};color:var(--text-on-primary, #ffffff);${sizeStyles[btnSize]}`;
                }
                inner = `<div><a href="${block.meta.url}" target="_blank" class="cta-button" style="${btnStyleStr}">${block.meta.text}</a></div>`;
                break;
            }
            case 'embed':
                if (!block.meta.url) return '';
                let embedPreviewHtml = '';
                if (block.meta.platform === 'custom') {
                    embedPreviewHtml = `<div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;"><div style="display: flex; align-items: center; gap: 12px;"><div style="width: 40px; height: 40px; border-radius: 9999px; background: #e6e8f1; display: flex; align-items: center; justify-content: center; font-size: 20px;">🔗</div><div><p style="font-size: 14px; font-weight: 500; color: #334155; margin: 0;">External Link</p><p style="font-size: 12px; color: #64748b; margin: 0; word-break: break-all;">${block.meta.originalUrl || block.meta.url}</p></div></div><a href="${block.meta.originalUrl || block.meta.url}" target="_blank" style="font-size: 12px; font-weight: 600; color: #141f52; text-decoration: none;">Open ↗</a></div>`;
                } else {
                    embedPreviewHtml = `<div class="embed-wrapper"><iframe src="${block.meta.url}" loading="lazy" style="width: 100%; border-radius: var(--border-radius, 10px); min-height: 280px; border: none;"></iframe></div>`;
                }
                inner = `<figure>${embedPreviewHtml}${block.meta.caption ? `<figcaption style="font-size:12px;color:#64748b;text-align:center;margin-top:4px;">${linkifyHtml(block.meta.caption)}</figcaption>` : ''}</figure>`;
                break;
            default: return '';
        }
        return `<div data-preview-block-id="${block.id}" class="preview-block-item transition cursor-pointer hover:outline hover:outline-2 hover:outline-primary-400" style="${wrapperStyle}">${inner}</div>`;
    }

    function bindPreviewBlockSelection() {
        document.querySelectorAll('[data-preview-block-id]').forEach(el => {
            el.onclick = (e) => { if (document.getElementById('stylePanel').classList.contains('hidden')) return; e.stopPropagation(); const id = el.dataset.previewBlockId; selectPreviewBlock(id); };
        });
    }

    function getGlobalDefaultsForBlock(block) {
        let defaultBg, defaultTextColor;
        if (block.type === 'quote') {
            defaultBg = styleConfig.blockquoteBg;
            defaultTextColor = styleConfig.mutedColor;
        } else if (block.type === 'cta') {
            defaultBg = styleConfig.background;
            defaultTextColor = styleConfig.bodyTextColor;
        } else if (['h1','h2','h3','h4','h5','h6'].includes(block.type)) {
            defaultBg = styleConfig.background;
            defaultTextColor = styleConfig.headingColor;
        } else {
            defaultBg = styleConfig.background;
            defaultTextColor = styleConfig.bodyTextColor;
        }
        return { defaultBg, defaultTextColor };
    }

    function selectPreviewBlock(id) {
        selectedPreviewBlockId = id;
        document.querySelectorAll('[data-preview-block-id]').forEach(el => el.classList.remove('preview-block-selected'));
        const el = document.querySelector(`[data-preview-block-id="${id}"]`);
        if (el) el.classList.add('preview-block-selected');
        const panel = document.getElementById('blockStylePanel');
        panel.classList.remove('hidden');
        const block = blocks.find(b => b.id === id);
        if (block) {
            if (!block.meta.styles) block.meta.styles = {};

            const buttonControls = document.getElementById('buttonBlockControls');
            if (block.type === 'cta') {
                buttonControls.classList.remove('hidden');
            } else {
                buttonControls.classList.add('hidden');
            }

            refreshBlockStylePanel();
        }
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function refreshBlockStylePanel() {
        if (!selectedPreviewBlockId) return;
        const block = blocks.find(b => b.id === selectedPreviewBlockId);
        if (!block) return;
        if (!block.meta.styles) block.meta.styles = {};
        const styles = block.meta.styles;
        const { defaultBg, defaultTextColor } = getGlobalDefaultsForBlock(block);

        document.getElementById('blockBgColor').value = styles.bgColor || defaultBg;
        document.getElementById('blockAlign').value = styles.align || 'left';
        document.getElementById('blockTextColor').value = styles.textColor || defaultTextColor;
        document.getElementById('blockFont').value = styles.font || 'inherit';
        document.getElementById('blockPadding').value = styles.padding || 0;
        document.getElementById('blockPaddingLabel').textContent = styles.padding || 0;

        if (block.type === 'cta') {
            const defaultBtnColor = (block.meta.style === 'teal' || block.meta.style === 'outline')
                ? styleConfig.primaryColor
                : styleConfig.accentColor;
            document.getElementById('buttonColor').value = styles.buttonColor || defaultBtnColor;
            document.getElementById('buttonSize').value = styles.buttonSize || 'medium';
        }
    }

    function clearPreviewBlockSelection() { selectedPreviewBlockId = null; document.querySelectorAll('[data-preview-block-id]').forEach(el => el.classList.remove('preview-block-selected')); document.getElementById('blockStylePanel').classList.add('hidden'); }

    function updatePreviewBlockStyle() {
        if (!selectedPreviewBlockId) return;
        const block = blocks.find(b => b.id === selectedPreviewBlockId);
        if (!block) return;
        if (!block.meta.styles) block.meta.styles = {};
        block.meta.styles.bgColor = document.getElementById('blockBgColor').value;
        block.meta.styles.align = document.getElementById('blockAlign').value;
        block.meta.styles.textColor = document.getElementById('blockTextColor').value;
        block.meta.styles.font = document.getElementById('blockFont').value;
        const pad = document.getElementById('blockPadding').value;
        block.meta.styles.padding = pad;
        document.getElementById('blockPaddingLabel').textContent = pad;

        if (block.type === 'cta') {
            block.meta.styles.buttonColor = document.getElementById('buttonColor').value;
            block.meta.styles.buttonSize = document.getElementById('buttonSize').value;
        }

        updatePreview();
        markUnsaved();
    }

    function setPreviewDevice(device) {
        const wrapper = document.getElementById('previewWrapper');
        if (device === 'mobile') { wrapper.classList.remove('device-desktop'); wrapper.classList.add('device-mobile'); document.getElementById('deviceMobile').classList.add('text-primary-600'); document.getElementById('deviceDesktop').classList.remove('text-primary-600'); }
        else { wrapper.classList.add('device-desktop'); wrapper.classList.remove('device-mobile'); document.getElementById('deviceDesktop').classList.add('text-primary-600'); document.getElementById('deviceMobile').classList.remove('text-primary-600'); }
    }

    function switchTab(tab) {
        ['preview','recipients','settings'].forEach(t => { document.getElementById('tab-' + t).classList.remove('active'); document.getElementById('pane-' + t).classList.add('hidden'); });
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('pane-' + tab).classList.remove('hidden');
    }

    function markUnsaved() {
        const status = document.getElementById('saveStatusText');
        status.textContent = 'Saving...';
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => { status.textContent = 'Unsaved changes'; }, 500);
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => { autoSaveDraft(); }, 5000);
    }

    function collectPayload() {
        return {
            title: document.getElementById('newsletterTitle').value,
            subject: document.getElementById('emailSubject').value,
            preview_text: document.getElementById('previewText').value,
            blocks: blocks,
            style: styleConfig,
            recipient_group: document.querySelector('input[name="recipientGroup"]:checked')?.value || 'self',
            sender_name: document.getElementById('senderName')?.value || 'Slough SEND PCF',
            reply_to: document.getElementById('replyTo')?.value || 'hello@sloughsend.org.uk',
            track_opens: document.getElementById('trackOpens')?.checked ?? true,
            track_clicks: document.getElementById('trackClicks')?.checked ?? true,
        };
    }

    function validateFields() {
        const title = document.getElementById('newsletterTitle').value.trim();
        const subject = document.getElementById('emailSubject').value.trim();
        if (!title || !subject) {
            showToast('Newsletter Title and Email Subject are required to save or send.', 'error');
            return false;
        }
        return true;
    }

    function autoSaveDraft() {
        const wire = getWire();
        if (!wire) return;
        if (!validateFields()) return;

        if (existingData.status === 'sent') {
            document.getElementById('saveStatusText').textContent = 'Unsaved changes (Sent)';
            return;
        }

        const status = document.getElementById('saveStatusText');
        status.textContent = 'Saving...';
        wire.save(collectPayload()).then((result) => {
            status.textContent = 'Saved as draft';
            if (result && result.id) {
                existingData.newsletterId = result.id;
                existingData.status = 'draft';
            }
        }).catch(error => {
            console.error('Auto-save error:', error);
            status.textContent = 'Save failed';
        });
    }

    function saveDraft() {
        const wire = getWire();
        if (!wire) { showToast('Still initializing, please try again...', 'error'); return; }
        if (!validateFields()) return;
        clearTimeout(autoSaveTimeout);

        const status = document.getElementById('saveStatusText');
        status.textContent = 'Saving...';
        wire.save(collectPayload()).then((result) => {
            showToast('Draft saved successfully');
            status.textContent = 'Saved as draft';
            if (result && result.id) {
                existingData.newsletterId = result.id;
                existingData.status = 'draft';
            }
        }).catch(error => {
            showToast('Error saving draft', 'error');
            console.error(error);
            status.textContent = 'Save failed';
        });
    }

    function openSendModal() {
        if (!validateFields()) return;
        const title = document.getElementById('newsletterTitle').value || 'this newsletter';
        document.getElementById('sendModalTitle').textContent = title;
        const recipientLabel = document.querySelector('input[name="recipientGroup"]:checked');
        const labels = { 
            all: 'all members', 
            all_subscribers: 'all members & public subscribers', 
            staff: 'admins & moderators', 
            self: 'yourself (test)' 
        };
        document.getElementById('sendModalRecipients').textContent = labels[recipientLabel?.value] || 'recipients';
        document.getElementById('sendModal').classList.remove('hidden');
        document.getElementById('confirmSend').checked = false;
        document.getElementById('confirmSendBtn').disabled = true;
    }
    function closeSendModal() { document.getElementById('sendModal').classList.add('hidden'); }
    document.addEventListener('change', (e) => { if (e.target.id === 'confirmSend') document.getElementById('confirmSendBtn').disabled = !e.target.checked; });

    function confirmSend() {
        const wire = getWire();
        if (!wire) { showToast('Still initializing, please try again...', 'error'); return; }
        clearTimeout(autoSaveTimeout);

        const btn = document.getElementById('confirmSendBtn');
        const cancelBtn = document.querySelector('#sendModal button[onclick="closeSendModal()"]');
        const originalBtnHtml = btn.innerHTML;
        
        btn.disabled = true;
        if (cancelBtn) cancelBtn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending...
        `;

        wire.send(collectPayload()).then((result) => {
            closeSendModal();
            
            btn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
            btn.innerHTML = originalBtnHtml;
            
            showToast(`Newsletter sent to ${result.count} recipients! 🎉`);
            document.getElementById('saveStatusText').textContent = 'Sent';
            if (result && result.id) {
                existingData.newsletterId = result.id;
                existingData.status = 'sent';
            }
        }).catch(error => {
            btn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
            btn.innerHTML = originalBtnHtml;
            
            let errorMessage = 'An error occurred while sending. Please try again later.';
            
            if (error && error.message && !error.message.includes('<!DOCTYPE')) {
                errorMessage = error.message;
            } else if (error && typeof error === 'string' && !error.includes('<!DOCTYPE')) {
                errorMessage = error;
            } else if (error && error.response && error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            }
            
            if (errorMessage.toLowerCase().includes('quota') || errorMessage.toLowerCase().includes('rate limit') || errorMessage.toLowerCase().includes('too many')) {
                errorMessage = 'Email provider rate limit reached or quota exceeded. Please wait a moment and try again.';
            }
            
            showToast(errorMessage, 'error');
            console.error('Send error details:', error);
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toastText');
        const toastIcon = document.getElementById('toastIcon');

        toastText.textContent = message;

        if (type === 'error') {
            toastIcon.innerHTML = `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            toastIcon.innerHTML = `<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
        }

        toast.classList.remove('hidden');
        clearTimeout(window.toastTimer);
        window.toastTimer = setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    function toggleFullscreenPreview() {
        const pane = document.getElementById('pane-preview');
        const header = document.getElementById('mainHeader');
        const maxIcon = document.getElementById('maximizeIcon');
        const minIcon = document.getElementById('minimizeIcon');
        pane.classList.toggle('preview-fullscreen');
        if (pane.classList.contains('preview-fullscreen')) {
            maxIcon.classList.add('hidden');
            minIcon.classList.remove('hidden');
            if (header) header.classList.add('header-fullscreen-active');
        } else {
            maxIcon.classList.remove('hidden');
            minIcon.classList.add('hidden');
            if (header) header.classList.remove('header-fullscreen-active');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindStyleControls();
        bindBlockStyleControls();
        document.body.addEventListener('click', (e) => {
            if (e.target.closest('a') && e.target.closest('#blockEditor')) {
                e.preventDefault();
            }
        });

        if (existingData && existingData.newsletterId) {
            document.getElementById('newsletterTitle').value = existingData.title || '';
            document.getElementById('emailSubject').value = existingData.subject || '';
            document.getElementById('previewText').value = existingData.previewText || '';
            document.getElementById('previewCount').textContent = (existingData.previewText || '').length;
            if (existingData.blocks && existingData.blocks.length > 0) {
                blocks = existingData.blocks;
                blockIdCounter = Math.max(0, ...blocks.map(b => parseInt((b.id || 'block-0').replace('block-', ''))));
            }
        if (existingData.style) {
            Object.assign(styleConfig, existingData.style);
        }
        Object.entries(styleConfig).forEach(([k, v]) => {
            const el = document.querySelector(`[data-style-key="${k}"]`);
            if (el) { 
                if (el.type === 'checkbox') el.checked = v; 
                else if (el.type === 'radio') { if (el.value == v) el.checked = true; }
                else el.value = v; 
            }
        });
        }
        applyStyleToPreview();
        if (blocks.length === 0 && !existingData.newsletterId) {
            addBlock('h1', null, 'Welcome to our October Update');
            addBlock('paragraph', 'block-1', 'Hello everyone, and welcome to this month\'s edition of the Slough SEND Parent Carer Forum newsletter.');
            addBlock('h2', 'block-2', 'What\'s new this month');
            addBlock('bulletList', 'block-3', '<li>New co-production meeting dates announced</li><li>Updated resources for EHCP applications</li><li>Drop-in sessions every Wednesday</li>');
            addBlock('image', 'block-4');
            addBlock('quote', 'block-5', 'Together, we make sure every family\'s voice is heard.');
            addBlock('cta', 'block-6');
        } else if (blocks.length > 0) {
            renderBlocks();
        }
        updatePreview();
        updateBlockCount();
        setPreviewDevice('desktop');
        document.getElementById('saveStatusText').textContent = existingData.newsletterId ? (existingData.status === 'sent' ? 'Sent' : 'Saved as draft') : 'Not saved yet';
    });

    document.getElementById('previewText').addEventListener('input', () => {
        document.getElementById('previewCount').textContent = document.getElementById('previewText').value.length;
    });
</script>
@endpush