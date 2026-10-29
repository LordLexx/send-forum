<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

new #[Layout('layouts.master')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $topic = '';
    public string $subject = '';
    public string $message = '';
    public bool $consent = false;

    public bool $showSuccess = false;
    public bool $showWelcomeModal = false;
    public ?string $submittedEmail = null;

    public function mount(): void
    {
        // Check if the user was redirected from the email verification link
        if (session()->has('contact_verified')) {
            $this->showWelcomeModal = true;
        }
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'topic'   => ['required', Rule::in(['general', 'event', 'steering'])],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate();

        $token = Str::random(60);

        $submission = \App\Models\ContactSubmission::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'topic'               => $validated['topic'],
            'subject'             => $validated['subject'],
            'message'             => $validated['message'],
            'consent'             => $validated['consent'],
            'status'              => \App\Models\ContactSubmission::STATUS_NEW,
            'ip_address'          => request()->ip(),
            'is_verified'         => false,
            'verification_token'  => $token,
        ]);

        // Send verification email to the submitter (NOT to admin yet)
        try {
            Mail::send(
                'emails.contact-verify',
                ['submission' => $submission, 'token' => $token],
                function ($message) use ($submission) {
                    $message->to($submission->email)
                            ->from('info@sloughsendpcf.org', 'Slough SEND PCF')
                            ->subject('Please verify your email address');
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send contact verification email: ' . $e->getMessage());
        }

        $this->submittedEmail = $validated['email'];
        $this->reset(['name', 'email', 'topic', 'subject', 'message', 'consent']);
        $this->showSuccess = true;
    }
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Get In Touch</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Contact Us</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Whether you've got a question, or you're interested in getting more involved with the forum, we'd love to hear from you.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)"
                     class="mb-10 bg-red-50 border border-red-200 text-red-900 rounded-2xl p-5 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Something went wrong</p>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-12">

                <!-- ===== FORM ===== -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 md:p-10">

                        @if ($showSuccess)
                            <!-- Success State -->
                            <div class="text-center py-8">
                                <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-4xl mb-5">
                                    📧
                                </div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Almost done!</h2>
                                <p class="text-gray-600 mb-2 max-w-md mx-auto">We've sent a verification link to</p>
                                <p class="font-bold text-gray-900 mb-6">{{ $submittedEmail }}</p>
                                <p class="text-sm text-gray-500 mb-8 max-w-md mx-auto">Please check your inbox (and spam folder) and click the link to confirm your enquiry. We won't receive your message until you verify.</p>
                                <button wire:click="$set('showSuccess', false)" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Submit another message
                                </button>
                            </div>
                        @else
                            <!-- Form -->
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Send us a message</h2>
                            <p class="text-gray-600 mb-8">Fields marked with <span class="text-orange-500">*</span> are required.</p>

                            <form wire:submit="submit" class="space-y-6">

                                <!-- Name & Email -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your name <span class="text-orange-500">*</span></label>
                                        <input type="text" id="name" wire:model="name"
                                               class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('name') border-red-400 @enderror"
                                               placeholder="Jane Smith">
                                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address <span class="text-orange-500">*</span></label>
                                        <input type="email" id="email" wire:model="email"
                                               class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('email') border-red-400 @enderror"
                                               placeholder="jane@example.com">
                                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Topic -->
                                <div>
                                    <label for="topic" class="block text-sm font-semibold text-gray-700 mb-2">What's this about? <span class="text-orange-500">*</span></label>
                                    <select id="topic" wire:model="topic"
                                            class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition bg-white @error('topic') border-red-400 @enderror">
                                        <option value="" disabled>Select a topic…</option>
                                        <option value="general">General enquiry</option>
                                        <option value="event">Event booking</option>
                                        <option value="steering">Joining the steering committee</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-2">This helps us route your message.</p>
                                    @error('topic') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject <span class="text-orange-500">*</span></label>
                                    <input type="text" id="subject" wire:model="subject"
                                           class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('subject') border-red-400 @enderror"
                                           placeholder="A short summary of your message">
                                    @error('subject') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Message -->
                                <div>
                                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message <span class="text-orange-500">*</span></label>
                                    <textarea id="message" rows="6" wire:model="message"
                                              class="w-full rounded-xl border-gray-200 border px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition resize-y @error('message') border-red-400 @enderror"
                                              placeholder="Tell us a bit more about why you're getting in touch…"></textarea>
                                    @error('message') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Consent -->
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" wire:model="consent"
                                               class="mt-1 mr-3 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 @error('consent') border-red-400 @enderror">
                                        <span class="text-sm text-gray-700">
                                            I'm happy for Slough SEND PCF to store my details in order to respond to my enquiry, in line with the
                                            <a href="#" class="text-primary-600 underline hover:text-primary-700">privacy policy</a>.
                                        </span>
                                    </label>
                                    @error('consent') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                                </div>

                                <!-- Submit -->
                                <div class="flex items-center justify-between flex-wrap gap-4">
                                    <button type="submit"
                                            class="inline-flex items-center justify-center bg-orange-500 text-white px-8 py-4 rounded-full font-medium hover:bg-orange-600 transition shadow-lg disabled:opacity-60 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled"
                                            wire:target="submit">
                                        <svg wire:loading wire:target="submit" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="submit">Send Message</span>
                                        <span wire:loading wire:target="submit">Sending…</span>
                                    </button>
                                    <p class="text-xs text-gray-500">We typically reply within 1–2 working days.</p>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- ===== SIDEBAR ===== -->
                <div class="space-y-8">

                    <!-- Why join -->
                    <div class="bg-gradient-to-br from-primary-600 to-primary-500 text-white p-8 rounded-3xl">
                        <h3 class="text-xl font-bold mb-2">Why you should join</h3>
                        <p class="text-primary-50 text-sm mb-6">Becoming a member is free and gives you a real voice in shaping local SEND support.</p>
                        <ul class="space-y-4 text-sm">
                            <li class="flex items-start"><span class="mr-3 mt-0.5 shrink-0">📣</span> Share your experiences and views</li>
                            <li class="flex items-start"><span class="mr-3 mt-0.5 shrink-0">📩</span> Receive SEND-related news, events and surveys</li>
                            <li class="flex items-start"><span class="mr-3 mt-0.5 shrink-0">🧩</span> Contribute to shaping local SEND policy and services with the official forum</li>
                        </ul>
                        @guest
                            <a href="{{ route('register') }}" wire:navigate
                               class="mt-6 inline-block w-full text-center bg-white text-primary-700 px-6 py-3 rounded-full font-semibold hover:bg-primary-50 transition">
                                Become a Member
                            </a>
                        @endguest
                    </div>

                    <!-- Other ways to reach us -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Other ways to reach us</h3>
                        <div class="space-y-4">
                            <div class="flex items-start bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                                <div class="w-11 h-11 bg-primary-100 rounded-full flex items-center justify-center text-xl mr-4 shrink-0">✉️</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">Email</h4>
                                    <a href="mailto:info@sloughsendpcf.org" class="text-sm text-primary-600 hover:underline break-all">info@sloughsendpcf.org</a>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                                <div class="w-11 h-11 bg-primary-100 rounded-full flex items-center justify-center text-xl mr-4 shrink-0">📍</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">Location</h4>
                                    <p class="text-sm text-gray-500">Slough, Berkshire</p>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                                <div class="w-11 h-11 bg-primary-100 rounded-full flex items-center justify-center text-xl mr-4 shrink-0">⏱️</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">Response Time</h4>
                                    <p class="text-sm text-gray-500">Within 1–2 working days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CTA + SOCIAL ===== -->
            <div class="mt-20 grid md:grid-cols-2 gap-8 items-stretch">

                <!-- Call To Action -->
                <div class="bg-gray-900 text-white p-8 md:p-10 rounded-3xl flex flex-col">
                    <h3 class="text-2xl md:text-3xl font-bold mb-3 leading-tight">
                        You're not on your own
                        <span class="text-orange-400 block sm:inline">#EveryVoiceMatters</span>
                    </h3>
                    <p class="text-gray-300 mb-8 flex-grow leading-relaxed">Our community brings parent carers together to share experiences, support each other and help to shape the future of SEND support. Find out how you can get involved, attend events and contribute to the forum.</p>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-full font-bold transition shadow-lg text-sm">View Events</a>
                        <a href="{{ route('newsletters.index') }}" wire:navigate class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-900 px-5 py-3 rounded-full font-bold transition shadow-lg text-sm">Sign up for our newsletter</a>
                        <a href="{{ route('resource.hub') }}" wire:navigate class="inline-flex items-center justify-center border-2 border-white text-white hover:bg-white/10 px-5 py-3 rounded-full font-bold transition text-sm">Our Resources</a>
                    </div>
                </div>

                <!-- Social -->
                <div class="bg-primary-50 p-8 md:p-10 rounded-3xl border border-primary-100 flex flex-col">
                    <h3 class="text-2xl font-bold text-primary-900 mb-2">Follow Us</h3>
                    <p class="text-gray-600 mb-6 flex-grow">You can also reach out through our social media accounts and engage with us through those platforms as well.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://www.facebook.com/profile.php?id=61588066225465" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-[#1877F2] text-white px-5 py-3 rounded-full font-medium hover:opacity-90 transition">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        <a href="https://www.instagram.com/sloughsendpcf" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white px-5 py-3 rounded-full font-medium hover:opacity-90 transition">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            Instagram
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===== FUNDING NOTE ===== -->
            <div class="mt-16 text-center">
                <div class="inline-flex flex-wrap items-center justify-center gap-x-3 gap-y-2 bg-gray-50 border border-gray-100 rounded-full px-6 py-3 text-sm text-gray-600">
                    <span class="text-lg">🏛️</span>
                    <span>Funded by the <strong class="text-gray-800">Department for Education</strong></span>
                    <span class="hidden sm:inline text-gray-300">•</span>
                    <span>A member of the <strong class="text-gray-800">National Network of Parent Carer Forums</strong></span>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== CTA BANNER ===== -->
    <section class="bg-gray-900 py-16">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-4">Prefer to Talk to Other Parents?</h2>
            <p class="text-gray-300 mb-8 text-lg">Join our private community to connect, share and learn from other Slough SEND families.</p>
            @guest
                <a href="{{ route('register') }}" wire:navigate class="inline-block bg-orange-500 text-white px-8 py-4 rounded-full font-medium hover:bg-orange-600 transition text-lg shadow-lg">Request Access</a>
            @endguest
            @auth
                <a href="{{ route('feed') }}" wire:navigate class="inline-block bg-orange-500 text-white px-8 py-4 rounded-full font-medium hover:bg-orange-600 transition text-lg shadow-lg">Go to Community Feed</a>
            @endauth
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

                <h3 class="text-2xl font-bold text-gray-900 mb-2">Thanks for verifying!</h3>
                <p class="text-gray-500 mb-6">Your email has been verified and your enquiry has been sent to our team. We'll be in touch within 1–2 working days.</p>

                <button wire:click="$set('showWelcomeModal', false)" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold transition shadow-sm text-sm">
                    Continue
                </button>
            </div>
        </div>
    @endif
</div>
