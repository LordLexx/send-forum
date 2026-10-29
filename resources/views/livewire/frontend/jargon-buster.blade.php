<?php
// resources/views/livewire/frontend/jargon-buster.blade.php

use App\Models\JargonTerm;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {

    public function with(): array
    {
        return [
            // Published only — drafts stay admin-side. Alphabetical, like a dictionary.
            // Cards never use extra_content (up to 10KB of HTML per term), so we
            // don't pull it — keeps the payload lean on big dictionaries.
            'terms' => JargonTerm::published()
                ->orderBy('term')
                ->select(['id', 'term', 'full_form', 'definition', 'slug'])
                ->get(),
        ];
    }
};
?>

@section('page_title', 'SEND Jargon Buster')
@section('meta_description', 'Demystifying terms and phrases you will hear on your SEND carer journey.')

<div> {{-- Single root element — Livewire requires exactly one --}}
    {{-- ===== PAGE HEADER ===== --}}
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Guidance & Help</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">SEND Jargon Buster</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Demystifying terms and phrases you will hear on your SEND carer journey.</p>
        </div>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($terms->isEmpty())
                {{-- Nothing published yet — friendly placeholder instead of search/filters --}}
                <div class="max-w-2xl mx-auto bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">The dictionary is on its way</h3>
                    <p class="text-gray-500">We're busy writing plain-English explanations for all the SEND terms. Check back soon!</p>
                </div>
            @else

                {{-- Big Search Bar --}}
                <div class="max-w-3xl mx-auto mb-12">
                    <div class="relative">
                        <input
                            type="text"
                            id="jargonSearch"
                            placeholder="Search for a term or acronym..."
                            autocomplete="off"
                            class="w-full pl-14 pr-6 py-6 text-lg rounded-full border-2 border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none shadow-md transition-all"
                        >
                        <div class="absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Alphabet Filter — All + A–Z, wired up for real --}}
                <div id="alphabetFilter" class="flex flex-wrap justify-center gap-2 mb-12">
                    <button type="button" data-letter=""
                        class="letter-btn h-10 px-4 rounded-full font-bold transition bg-primary-600 text-white hover:bg-primary-700">All</button>
                    @foreach(range('A', 'Z') as $letter)
                        <button type="button" data-letter="{{ $letter }}"
                            class="letter-btn w-10 h-10 rounded-full font-bold transition bg-white text-gray-600 hover:bg-primary-50 border border-gray-200">{{ $letter }}</button>
                    @endforeach
                </div>

                {{-- Jargon Grid --}}
                <div id="jargonContainer" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach($terms as $term)
                        <a href="{{ route('jargon.show', $term->slug) }}"
                            data-letter="{{ strtoupper(substr($term->term, 0, 1)) }}"
                            class="jargon-item block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <h3 class="text-xl font-bold text-primary-700 mb-2">{{ $term->term }}</h3>
                            @if($term->full_form)
                                <p class="text-sm text-gray-600 font-semibold mb-2">{{ $term->full_form }}</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ $term->definition }}</p>
                        </a>
                    @endforeach

                </div>

                {{-- Empty State (no search/letter matches) --}}
                <div id="noResults" class="hidden text-center py-12">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No terms found</h3>
                    <p class="text-gray-500">Try searching for a different acronym or term. If you think something is missing, let us know!</p>
                </div>

            @endif
        </div>
    </section>

    <script>
        // Everything wrapped in an IIFE so nothing leaks into the global scope —
        // important because wire:navigate re-executes this script when users
        // come back to the page, and a top-level `let` would throw a
        // "already declared" error the second time around.
        (function () {
            var activeLetter = '';

            var input = document.getElementById('jargonSearch');
            var container = document.getElementById('jargonContainer');
            var noResults = document.getElementById('noResults');
            var letterButtons = document.querySelectorAll('#alphabetFilter .letter-btn');

            function filterJargon() {
                if (!container) return;

                var filter = (input ? input.value : '').toUpperCase().trim();
                var items = container.getElementsByClassName('jargon-item');
                var visibleCount = 0;

                for (var i = 0; i < items.length; i++) {
                    // Matches the term AND the full form AND the definition —
                    // the original template only read the first <p>, so
                    // definitions were unsearchable on cards with a full form.
                    var text = items[i].textContent.toUpperCase();
                    var letter = items[i].getAttribute('data-letter') || '';

                    var matchesSearch = filter === '' || text.indexOf(filter) > -1;
                    var matchesLetter = activeLetter === '' || letter === activeLetter;

                    if (matchesSearch && matchesLetter) {
                        items[i].style.display = "";
                        visibleCount++;
                    } else {
                        items[i].style.display = "none";
                    }
                }

                if (visibleCount === 0) {
                    container.classList.add('hidden');
                    if (noResults) noResults.classList.remove('hidden');
                } else {
                    container.classList.remove('hidden');
                    if (noResults) noResults.classList.add('hidden');
                }
            }

            function setActiveButton(btn) {
                letterButtons.forEach(function (b) {
                    var isActive = b === btn;
                    // Active: solid primary pill. Inactive: white pill with border.
                    // These classes all appear in the server-rendered markup,
                    // so they exist in the compiled Tailwind CSS.
                    b.classList.toggle('bg-primary-600', isActive);
                    b.classList.toggle('text-white', isActive);
                    b.classList.toggle('hover:bg-primary-700', isActive);
                    b.classList.toggle('bg-white', !isActive);
                    b.classList.toggle('text-gray-600', !isActive);
                    b.classList.toggle('hover:bg-primary-50', !isActive);
                    b.classList.toggle('border', !isActive);
                    b.classList.toggle('border-gray-200', !isActive);
                });
            }

            // Search — 'input' catches typing, pasting, AND the browser's
            // native clear (X) button, unlike onkeyup.
            if (input) {
                input.addEventListener('input', filterJargon);
            }

            // Alphabet buttons — combine with whatever is in the search box
            letterButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    activeLetter = this.getAttribute('data-letter') || '';
                    setActiveButton(this);
                    filterJargon();
                });
            });
        })();
    </script>
</div>