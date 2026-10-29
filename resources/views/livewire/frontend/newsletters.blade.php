<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

new #[Layout('layouts.master')] class extends Component {
    public ?string $email = null;
    public bool $showSuccess = false;
    public bool $showWelcomeModal = false;

    public function mount(): void
    {
        // Check if the user was redirected from the email verification link
        if (session()->has('newsletter_verified')) {
            $this->showWelcomeModal = true;
        }
    }

    public function with(): array
    {
        return [
            // Only expose newsletters the admin explicitly sent to ALL members
            // (the "All Members" or "All Members & Subscribers" options).
            // Newsletters sent via "Send to Self (Test)" or "Admins & Moderators"
            // are never shown on the public archive.
            'newsletters' => Newsletter::where('status', 'sent')
                ->whereIn('recipient_group', ['all', 'all_subscribers'])
                ->orderBy('sent_at', 'desc')
                ->paginate(12)
        ];
    }

    public function subscribe()
    {
        $validated = $this->validate([
            'email' => ['required', 'email', Rule::unique('newsletter_subscribers', 'email')],
        ]);

        $token = Str::random(60);

        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'is_verified' => false,
            'verification_token' => $token,
        ]);

        // --- SEND VERIFICATION EMAIL ---
        Mail::send('emails.newsletter-verify', ['subscriber' => $subscriber, 'token' => $token], function ($message) use ($subscriber) {
            $message->to($subscriber->email)->subject('Please verify your email address');
        });
        // ------------------------------

        $this->reset('email');
        $this->showSuccess = true;
    }
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1597392582469-a697322d5c16?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Stay Updated</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Past Newsletters</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Catch up on all our previous updates, news, and resources sent to the Slough SEND community.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Subscribe Box -->
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-12">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-3xl shrink-0">
                        ✉️
                    </div>
                    <div class="flex-grow text-center md:text-left">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Never miss an update</h3>
                        <p class="text-sm text-gray-500">You don't need to be a member to stay informed. Subscribe with your email to receive our newsletters directly.</p>
                    </div>
                </div>

                <div class="mt-6">
                    @if ($showSuccess)
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
                            <span>📧 Almost done! Please check your email to verify your subscription.</span>
                            <button wire:click="$set('showSuccess', false)" class="text-blue-500 hover:text-blue-700">&times;</button>
                        </div>
                    @else
                        <form wire:submit="subscribe" class="flex flex-col sm:flex-row gap-3">
                            <input type="email" wire:model="email" placeholder="Enter your email address" class="flex-grow px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">

                            <button type="submit" wire:loading.attr="disabled" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold transition shadow-sm text-sm whitespace-nowrap flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="subscribe" class="flex items-center">
                                    Subscribe Now
                                </span>
                                <span wire:loading wire:target="subscribe" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Subscribing...
                                </span>
                            </button>
                        </form>
                        @error('email') <p class="text-red-500 text-xs mt-2 text-left">{{ $message }}</p> @enderror
                    @endif
                </div>
            </div>

            <!-- Newsletters Grid -->
            @if ($newsletters->isEmpty())
                <div class="text-center py-16">
                    <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-2xl mb-4 shadow-sm">📭</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Newsletters Yet</h3>
                    <p class="text-gray-500">Check back soon for our latest updates!</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($newsletters as $newsletter)
                        <a href="{{ route('newsletters.show', $newsletter) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 block group overflow-hidden">
                            <div class="bg-gradient-to-br from-primary-600 to-primary-800 p-6">
                                <p class="text-xs text-primary-100 uppercase tracking-wide font-semibold">
                                    {{ \Carbon\Carbon::parse($newsletter->sent_at)->format('j F Y') }}
                                </p>
                            </div>
                            <div class="p-6">
                                <h3 class="font-bold text-lg text-gray-900 mb-2 group-hover:text-primary-700 transition line-clamp-2">{{ $newsletter->title }}</h3>
                                <p class="text-sm text-gray-500 line-clamp-3">{{ $newsletter->preview_text ?? strip_tags($newsletter->blocks[0]['content'] ?? '') }}</p>
                                <span class="inline-flex items-center text-primary-600 font-semibold text-sm mt-4 group-hover:gap-2 gap-1 transition-all">
                                    Read Newsletter
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $newsletters->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- ===== WELCOME MODAL ===== -->
    @if ($showWelcomeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click="self">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8 text-center relative" wire:click.stop>
                <button wire:click="$set('showWelcomeModal', false)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-5">
                    <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome to our Newsletter!</h3>
                <p class="text-gray-500 mb-6">Your email has been verified. You are now officially subscribed to receive updates from the Slough SEND Parent Carer Forum.</p>

                <button wire:click="$set('showWelcomeModal', false)" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold transition shadow-sm text-sm">
                    Continue Reading
                </button>
            </div>
        </div>
    @endif
</div>
