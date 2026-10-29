<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    public function with(): array
    {
        return [
            'policies' => [
                [
                    'title'       => "Children's Safeguarding Policy",
                    'description' => "How we keep children and young people safe, recognise abuse, and report concerns.",
                    'filename'    => "Slough-Childrens-Safeguarding-policy.pdf",
                    'icon_color'  => "bg-red-50 text-red-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />',
                ],
                [
                    'title'       => "Adult Safeguarding Policy",
                    'description' => "How we protect adults at risk of harm and respond to disclosures of abuse.",
                    'filename'    => "Slough-Adult-Safeguarding-Policy.pdf",
                    'icon_color'  => "bg-orange-50 text-orange-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                ],
                [
                    'title'       => "Equalities Policy",
                    'description' => "Our commitment to equal access, inclusion and non-discrimination for all families.",
                    'filename'    => "Slough-Equalities.pdf",
                    'icon_color'  => "bg-purple-50 text-purple-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                ],
                [
                    'title'       => "Health and Safety Policy",
                    'description' => "How we keep staff, volunteers and visitors safe during our activities.",
                    'filename'    => "Slough-Health-and-Safety-Policy-.pdf",
                    'icon_color'  => "bg-emerald-50 text-emerald-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                ],
                [
                    'title'       => "Acceptable Usage Policy",
                    'description' => "Guidelines on respectful use of our social media and digital platforms.",
                    'filename'    => "Slough-Acceptable-Usage-Policy.pdf",
                    'icon_color'  => "bg-blue-50 text-blue-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                ],
                [
                    'title'       => "Social Media Policy",
                    'description' => "Guidelines on how Slough SEND PCF's social media channels and personal accounts should be used.",
                    'filename'    => "Slough-Social-media-policy.pdf",
                    'icon_color'  => "bg-pink-50 text-pink-600",
                    'icon_svg'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />',
                ],
            ],
        ];
    }
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?q=80&w=2070&auto=format&fit=crop');"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Transparency & Accountability</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Our Policies</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">As an accountable organisation, we publish the policies that govern how we work. You're welcome to read or download any of them below.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Policy Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($policies as $policy)
                    <a href="{{ asset('policy-files/' . $policy['filename']) }}" target="_blank" download class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col hover:shadow-lg hover:border-primary-200 transition-all duration-300 group">
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-xl {{ $policy['icon_color'] }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $policy['icon_svg'] !!}</svg>
                        </div>

                        <!-- Title -->
                        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-primary-700 transition">{{ $policy['title'] }}</h3>

                        <!-- Description -->
                        <p class="text-sm text-gray-500 mb-6 flex-grow">{{ $policy['description'] }}</p>

                        <!-- Download Button -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="inline-flex items-center text-sm font-medium text-primary-600 group-hover:text-primary-800">
                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Download
                            </span>
                            <span class="text-xs text-gray-400">PDF</span>
                        </div>
                    </a>
                @endforeach

            </div>

            <!-- Info / Contact Block -->
            <div class="mt-16 bg-primary-50 border border-primary-100 rounded-2xl p-8 md:p-10">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl font-bold text-primary-900 mb-3">Questions about our policies?</h2>
                    <p class="text-gray-600 mb-6">If you'd like to know more about any of our policies or have a question about how we work, please don't hesitate to get in touch.</p>
                    <a href="{{ route('contact') }}" wire:navigate class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                        Contact Us
                        <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>
