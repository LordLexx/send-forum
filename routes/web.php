<?php

use App\Http\Middleware\CheckUserStatus;
use App\Livewire\Welcome;
use App\Livewire\Notifications\Bell;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', Welcome::class)->name('welcome');

// Change to feed route!
Volt::route('feed', 'frontend.feed')->middleware(['auth', 'verified', CheckUserStatus::class])->name('feed');
Volt::route('category/{category:slug}', 'frontend.category')->middleware(['auth', 'verified', CheckUserStatus::class])->name('category');
Volt::route('saved-posts', 'frontend.saved-posts')->middleware(['auth', 'verified', CheckUserStatus::class])->name('saved.posts');
Volt::route('search', 'frontend.search')->middleware(['auth', 'verified', CheckUserStatus::class])->name('search');
Volt::route('resources', 'frontend.resources')->middleware(['auth', 'verified', CheckUserStatus::class])->name('resources');
Volt::route('events', 'frontend.events')->name('events.index');
Volt::route('events/{event}', 'frontend.event-show')->name('events.show');
Volt::route('about', 'frontend.about')->name('about');

// Newsletters (public — no auth, same as About/Support/Events)
Volt::route('newsletters', 'frontend.newsletters')->name('newsletters.index');
Volt::route('newsletters/{newsletter}', 'frontend.newsletter-show')->name('newsletters.show');

// Newsletter Verification
Route::get('/newsletter/verify/{token}', function ($token) {
    $subscriber = NewsletterSubscriber::where('verification_token', $token)->first();

    if (!$subscriber) {
        return redirect('/')->with('error', 'Invalid or expired verification link.');
    }

    if (!$subscriber->is_verified) {
        $subscriber->is_verified = true;
        $subscriber->verification_token = null;
        $subscriber->save();

        Mail::send('emails.newsletter-welcome', ['subscriber' => $subscriber], function ($message) use ($subscriber) {
            $message->to($subscriber->email)->subject('Welcome to the Slough SEND Newsletter');
        });
    }

    // Redirect back to the newsletters page with a flash message
    return redirect()->route('newsletters.index')->with('newsletter_verified', true);
})->name('newsletter.verify');

Route::get('/newsletter/verified', function () {
    return view('newsletter.verified'); // Create a simple blade file for this or redirect to home
})->name('newsletter.verified.success');

Volt::route('resource-hub', 'frontend.resource-hub')->name('resource.hub');
Volt::route('fathers-hub', 'frontend.fathers-hub')->name('fathers.hub');
Volt::route('contact', 'frontend.contact')->name('contact');
// Contact Verification
Route::get('/contact/verify/{token}', function ($token) {
    $submission = \App\Models\ContactSubmission::where('verification_token', $token)->first();

    if (!$submission) {
        return redirect()->route('contact')->with('error', 'Invalid or expired verification link.');
    }

    if (!$submission->is_verified) {
        $submission->is_verified = true;
        $submission->verification_token = null;
        $submission->verified_at = now();
        $submission->save();

        // Now send the admin notification email (only after verification)
        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.contact-submission-received',
                ['submission' => $submission],
                function ($message) use ($submission) {
                    $message->to('info@sloughsendpcf.org')
                            ->from('info@sloughsendpcf.org', 'Slough SEND PCF')
                            ->replyTo($submission->email, $submission->name)
                            ->subject('New Contact: ' . $submission->subject);
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send contact notification email: ' . $e->getMessage());
        }

        // Send confirmation email to the user
        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.contact-verified',
                ['submission' => $submission],
                function ($message) use ($submission) {
                    $message->to($submission->email)
                            ->from('info@sloughsendpcf.org', 'Slough SEND PCF')
                            ->subject('We\'ve received your enquiry');
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send contact verified email: ' . $e->getMessage());
        }
    }

    return redirect()->route('contact')->with('contact_verified', true);
})->name('contact.verify');
Volt::route('support', 'frontend.support')->name('support');
Volt::route('support/rights-and-entitlements', 'frontend.rights-and-entitlements')->name('support.rights');
Volt::route('support/local-and-national-support', 'frontend.local-and-national-support')->name('support.services');
Volt::route('support/benefits-and-finance', 'frontend.benefits-and-finance')->name('support.benefits');

// Legal & Policy pages (public)
Volt::route('cookie-policy', 'frontend.cookie-policy')->name('cookie.policy');
Volt::route('privacy-policy', 'frontend.privacy-policy')->name('privacy.policy');
Volt::route('policies', 'frontend.policies')->name('policies.index');

Volt::route('categories', 'frontend.all-categories')->middleware(['auth', 'verified', CheckUserStatus::class])->name('categories.all');


// Jargon Buster (public — no auth, same as About/Support/Events)
Volt::route('jargon-buster', 'frontend.jargon-buster')->name('jargon.index');
Volt::route('jargon-buster/{slug}', 'frontend.jargon-show')->name('jargon.show');

Volt::route('profile', 'profile.show')->middleware(['auth', 'verified', CheckUserStatus::class])->name('profile');
Volt::route('profile/edit', 'profile.edit')->middleware(['auth', CheckUserStatus::class])->name('profile.edit');
Volt::route('profile/account', 'profile.account')->middleware(['auth', CheckUserStatus::class])->name('profile.account');
Volt::route('post/{post}', 'frontend.post-show')
    ->middleware(['auth', 'verified', CheckUserStatus::class])
    ->name('posts.show');

Route::get('notifications/{id}/read', function ($id) {
    if (auth()->check()) {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return redirect($notification->data['url'] ?? route('feed'));
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('notifications.read');

Volt::route('pending', 'pages.auth.pending')->name('pending');
Volt::route('verify-email/{id}', 'pages.auth.verify-email')->name('verify.email');
Volt::route('still-pending', 'pages.auth.still-pending')->name('still.pending');
Volt::route('suspended', 'pages.auth.suspended')->name('suspended');
Volt::route('rejected', 'pages.auth.rejected')->name('rejected');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
