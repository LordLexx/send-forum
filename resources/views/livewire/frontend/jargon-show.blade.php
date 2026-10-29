<?php
// resources/views/livewire/frontend/jargon-show.blade.php

use App\Models\JargonTerm;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {

    public JargonTerm $term;

    public function mount(string $slug): void
    {
        // Published only — a draft term 404s on the public site,
        // even if an admin clicks "View" too early.
        $this->term = JargonTerm::published()
            ->where('slug', $slug)
            ->firstOrFail();
    }
};
?>

@section('page_title', $term->term . ($term->full_form ? ' – ' . $term->full_form : '') . ' · Jargon Buster')
@section('meta_description', Str::limit($term->definition, 155))

{{-- ===== MAIN CONTENT ===== --}}
<section class="pt-32 pb-20 md:pt-40 md:pb-24 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back Button --}}
        <a href="{{ route('jargon.index') }}" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700 mb-8 group transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Jargon Buster
        </a>

        {{-- Article Card --}}
        <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100">

            {{-- Title Section --}}
            <div class="mb-8 pb-8 border-b border-gray-100">
                <span class="inline-block bg-primary-50 text-primary-700 px-3 py-1 rounded-full text-xs font-bold mb-4 uppercase tracking-wide">Term</span>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                    <span class="text-primary-600">{{ $term->term }}</span>@if($term->full_form) – {{ $term->full_form }}@endif
                </h1>
            </div>

            {{-- Description Section --}}
            <div class="prose max-w-none">
                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    {{ $term->definition }}
                </p>

                {{-- Rich HTML from the admin panel's "Extra page content" field --}}
                {!! $term->extra_content !!}
            </div>

            {{-- Footer of Article Card --}}
            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">Still have questions about this term?</p>
                <a href="{{ auth()->check() ? route('feed') : route('login') }}"
                    class="bg-gray-900 text-white px-6 py-3 rounded-full font-medium hover:bg-gray-800 transition shadow-sm text-sm inline-flex items-center">
                    Ask the Forum
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>

        </div>

    </div>
</section>