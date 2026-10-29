<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Event; // <-- ADD THIS IMPORT
use App\Models\Newsletter;

Volt::route('admin/login', 'pages.auth.admin-login')
    ->name('admin.login');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Volt::route('dashboard', 'admin.dashboard')
        ->name('dashboard');

    Volt::route('members', 'admin.members')
        ->name('members');

    // Admin Member Profile View
    Volt::route('members/{user}', 'admin.members-show')
        ->name('members.show');

    // Post Moderation Routes
    Volt::route('posts', 'admin.posts')
        ->name('posts');

    Volt::route('posts/{post}', 'admin.post-show')
        ->name('posts.show');

    // Category Routes
    Volt::route('categories', 'admin.categories')
        ->name('categories');

    Volt::route('categories/create', 'admin.category-form')
        ->name('categories.create');

    Volt::route('categories/{id}/edit', 'admin.category-form')
        ->name('categories.edit');

    // Event Routes
    Volt::route('events', 'admin.events')
        ->name('events');

    Volt::route('events/create', 'admin.event-form')
        ->name('events.create');

    Volt::route('events/{event}/edit', 'admin.event-form')
        ->name('events.edit');

    // NEW: Event Guests Route
    Volt::route('events/{event}/guests', 'admin.event-guests')
        ->name('events.guests');

    // NEW: Event Guests CSV Download Route
    Route::get('events/{event}/guests/download', function (\App\Models\Event $event) {
        $filename = str()->slug($event->title) . '-guests.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($event) {
            $handle = fopen('php://output', 'w');
            // Add CSV headers
            fputcsv($handle, ['Email', 'Registered At']);

            // Add data
            foreach ($event->registrations as $registration) {
                fputcsv($handle, [$registration->email, $registration->created_at->format('Y-m-d H:i:s')]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    })->name('events.guests.download');

    // Jargon Buster Routes
    Volt::route('/jargon-buster', 'admin.jargon-index')->name('jargon');
    Volt::route('/jargon-buster/create', 'admin.jargon-form')->name('jargon.create');
    Volt::route('/jargon-buster/{jargonTerm}/edit', 'admin.jargon-form')->name('jargon.edit');

    // Resource Routes
    Volt::route('resources', 'admin.resources')
        ->name('resources');

    Volt::route('resources/create', 'admin.resource-form')
        ->name('resources.create');

    Volt::route('resources/{id}/edit', 'admin.resource-form')
        ->name('resources.edit');

    // Resource Download
    Route::get('resources/{resource}/download', function (\App\Models\Resource $resource) {
        if (! $resource->file_path || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        $filename = $resource->original_filename ?: basename($resource->file_path);

        return \Illuminate\Support\Facades\Storage::disk('public')
            ->download($resource->file_path, $filename);
    })->name('resources.download');

    // Newsletter routes
    Volt::route('newsletters', 'admin.newsletters')
        ->name('newsletters');

    Volt::route('newsletters/create', 'admin.newsletter-editor')
        ->name('newsletters.create');

    Volt::route('newsletters/{newsletter}/edit', 'admin.newsletter-editor')
        ->name('newsletters.edit');

    // Image upload endpoint for the editor
    Route::post('newsletters/upload-image', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $path = $request->file('image')->store('newsletter-images', 'public');

        return response()->json([
            'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
        ]);
    })->name('newsletters.upload-image');

    // Delete a draft
    Route::delete('newsletters/{newsletter}', function (Newsletter $newsletter) {
        $newsletter->delete();
        return redirect()->route('admin.newsletters')
            ->with('notify', ['type' => 'success', 'message' => 'Newsletter deleted.']);
    })->name('newsletters.destroy');

    // Newsletter Subscribers View
    Volt::route('newsletters/subscribers', 'admin.newsletter-subscribers')
        ->name('newsletters.subscribers');

    // Newsletter Subscribers CSV Export
    Route::get('newsletters/subscribers/export', function () {
        $filename = 'newsletter-subscribers.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            // Add CSV headers
            fputcsv($handle, ['Email', 'Verified', 'Subscribed At']);

            // Add data
            $subscribers = \App\Models\NewsletterSubscriber::all();
            foreach ($subscribers as $subscriber) {
                fputcsv($handle, [
                    $subscriber->email,
                    $subscriber->is_verified ? 'Yes' : 'No',
                    $subscriber->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    })->name('newsletters.subscribers.export');

    // Newsletter Subscribers CSV Import
    Volt::route('newsletters/subscribers/import', 'admin.newsletter-subscribers-import')
        ->name('newsletters.subscribers.import');

    // Contact Submissions
    Volt::route('contact-submissions', 'admin.contact-submissions')
        ->name('contact-submissions');

    Volt::route('contact-submissions/{submission}', 'admin.contact-submission-show')
        ->name('contact-submissions.show');

    Route::post('logout', function (\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    })->name('logout');
});
