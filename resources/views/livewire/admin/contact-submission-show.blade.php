<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

new #[Layout('layouts.admin-layout')] class extends Component {
    public ContactSubmission $submission;
    public string $newStatus;
    public string $adminNotes;
    public string $replyMessage = '';
    public bool $showReplyForm = false;

    public function mount(ContactSubmission $submission): void
    {
        // Block access to unverified submissions
        if (!$submission->is_verified) {
            $this->redirect(route('admin.contact-submissions'), navigate: true);
            return;
        }

        $this->submission  = $submission;
        $this->newStatus   = $submission->status;
        $this->adminNotes  = $submission->admin_notes ?? '';

        // Auto-mark as read when first viewed
        if ($submission->status === ContactSubmission::STATUS_NEW) {
            $submission->update(['status' => ContactSubmission::STATUS_READ]);
            $this->newStatus = ContactSubmission::STATUS_READ;
        }
    }

    public function updateStatus(): void
    {
        $this->submission->update(['status' => $this->newStatus]);
        $this->dispatch('notify', type: 'success', message: 'Status updated.');
    }

    public function saveNotes(): void
    {
        $this->submission->update(['admin_notes' => $this->adminNotes]);
        $this->dispatch('notify', type: 'success', message: 'Notes saved.');
    }

    public function sendReply(): void
    {
        $validated = $this->validate([
            'replyMessage' => ['required', 'string', 'min:10'],
        ]);

        try {
            Mail::send(
                'emails.contact-reply',
                [
                    'submission'    => $this->submission,
                    'replyMessage'  => $validated['replyMessage'],
                ],
                function ($message) {
                    $message->to($this->submission->email)
                            ->from('info@sloughsendpcf.org', 'Slough SEND PCF')
                            ->replyTo('info@sloughsendpcf.org', 'Slough SEND PCF')
                            ->subject('Re: ' . $this->submission->subject);
                }
            );

            $this->submission->update([
                'admin_reply' => $validated['replyMessage'],
                'replied_at'  => now(),
                'status'      => ContactSubmission::STATUS_RESPONDED,
            ]);

            $this->newStatus     = ContactSubmission::STATUS_RESPONDED;
            $this->replyMessage  = '';
            $this->showReplyForm = false;

            $this->dispatch('notify', type: 'success', message: 'Reply sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send contact reply: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to send reply. Please try again.');
        }
    }

    public function delete(): void
    {
        $this->submission->delete();
        $this->dispatch('notify', type: 'success', message: 'Submission deleted.');
        $this->redirect(route('admin.contact-submissions'), navigate: true);
    }
};
?>

<div>
    <!-- Back link -->
    <div class="mb-4">
        <a href="{{ route('admin.contact-submissions') }}" wire:navigate class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to submissions
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <div class="flex items-start justify-between mb-4 gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $submission->subject }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Received {{ $submission->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }} whitespace-nowrap">
                        {{ $submission->status_label }}
                    </span>
                </div>

                <div class="border-t border-gray-100 pt-4 mb-6">
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold mb-1">From</p>
                            <p class="font-medium text-gray-900">{{ $submission->name }}</p>
                            <a href="mailto:{{ $submission->email }}" class="text-primary-600 hover:underline">{{ $submission->email }}</a>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold mb-1">Topic</p>
                            <p class="font-medium text-gray-900">{{ $submission->topic_label }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Message</p>
                    <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $submission->message }}</p>
                </div>
            </div>

            <!-- Reply card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Reply</h2>
                    @if ($submission->replied_at)
                        <span class="text-xs text-gray-500">Last replied {{ $submission->replied_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>

                @if ($submission->admin_reply)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                        <p class="text-xs uppercase text-green-700 font-semibold mb-2">Previous Reply</p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->admin_reply }}</p>
                    </div>
                @endif

                @if ($showReplyForm)
                    <form wire:submit="sendReply" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Your reply to {{ $submission->name }}</label>
                            <textarea wire:model="replyMessage" rows="6"
                                      class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition resize-y @error('replyMessage') border-red-400 @enderror"
                                      placeholder="Type your reply here…"></textarea>
                            @error('replyMessage') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" wire:loading.attr="disabled" wire:target="sendReply"
                                    class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-full font-medium transition text-sm disabled:opacity-50">
                                <span wire:loading.remove wire:target="sendReply">Send Reply</span>
                                <span wire:loading wire:target="sendReply">Sending…</span>
                            </button>
                            <button type="button" wire:click="$set('showReplyForm', false)"
                                    class="text-gray-600 hover:text-gray-900 px-5 py-2.5 text-sm font-medium">Cancel</button>
                        </div>
                    </form>
                @else
                    <button wire:click="$set('showReplyForm', true)"
                            class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-full font-medium transition text-sm">
                        @if ($submission->admin_reply) Send Another Reply @else Reply to {{ $submission->name }} @endif
                    </button>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status changer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Status</h2>
                <form wire:submit="updateStatus" class="space-y-3">
                    <select wire:model="newStatus"
                            class="w-full rounded-lg border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none bg-white">
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="responded">Responded</option>
                        <option value="archived">Archived</option>
                    </select>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Admin notes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Admin Notes</h2>
                <form wire:submit="saveNotes" class="space-y-3">
                    <textarea wire:model="adminNotes" rows="5"
                              class="w-full rounded-lg border-gray-200 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none resize-y"
                              placeholder="Internal notes (not visible to the sender)…"></textarea>
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                        Save Notes
                    </button>
                </form>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Details</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Submitted</dt>
                        <dd class="font-medium text-gray-900">{{ $submission->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @if ($submission->verified_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Verified</dt>
                            <dd class="font-medium text-gray-900">{{ $submission->verified_at->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                    @if ($submission->replied_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Replied</dt>
                            <dd class="font-medium text-gray-900">{{ $submission->replied_at->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                    @if ($submission->ip_address)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">IP Address</dt>
                            <dd class="font-mono text-xs text-gray-700">{{ $submission->ip_address }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Danger zone -->
            <div class="bg-red-50 rounded-2xl border border-red-200 p-6">
                <h2 class="text-lg font-bold text-red-900 mb-2">Danger Zone</h2>
                <p class="text-xs text-red-700 mb-4">Deleting a submission is permanent and cannot be undone.</p>
                <button wire:click="delete" wire:confirm="Are you sure you want to delete this submission? This cannot be undone."
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                    Delete Submission
                </button>
            </div>
        </div>
    </div>
</div>
