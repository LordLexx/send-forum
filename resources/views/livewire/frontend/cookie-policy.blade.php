<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.master')] class extends Component {
    // Static page
};
?>

<div>
    <!-- ===== PAGE HEADER ===== -->
    <header class="pt-32 pb-16 md:pt-40 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-bg z-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-1 rounded-full text-sm font-medium mb-4">Legal</span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Cookie Policy</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Last updated on October 12, 2026 — applies to citizens and legal permanent residents of the United Kingdom.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 space-y-10">

                <!-- 1. Introduction -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>1. Introduction
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Our website, <a href="https://sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">https://sloughsendpcf.org</a> (hereinafter: "the website") uses cookies and other related technologies (for convenience all technologies are referred to as "cookies"). Cookies are also placed by third parties we have engaged. In the document below we inform you about the use of cookies on our website.</p>
                </section>

                <!-- 2. What are cookies? -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>2. What are cookies?
                    </h2>
                    <p class="text-gray-600 leading-relaxed">A cookie is a small simple file that is sent along with pages of this website and stored by your browser on the hard drive of your computer or another device. The information stored therein may be returned to our servers or to the servers of the relevant third parties during a subsequent visit.</p>
                </section>

                <!-- 3. What are scripts? -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>3. What are scripts?
                    </h2>
                    <p class="text-gray-600 leading-relaxed">A script is a piece of program code that is used to make our website function properly and interactively. This code is executed on our server or on your device.</p>
                </section>

                <!-- 4. What is a web beacon? -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>4. What is a web beacon?
                    </h2>
                    <p class="text-gray-600 leading-relaxed">A web beacon (or a pixel tag) is a small, invisible piece of text or image on a website that is used to monitor traffic on a website. In order to do this, various data about you is stored using web beacons.</p>
                </section>

                <!-- 5. Cookies -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>5. Cookies
                    </h2>

                    <div class="space-y-5 pl-4 border-l-2 border-primary-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">5.1 Technical or functional cookies</h3>
                            <p class="text-gray-600 leading-relaxed">Some cookies ensure that certain parts of the website work properly and that your user preferences remain known. By placing functional cookies, we make it easier for you to visit our website. This way, you do not need to repeatedly enter the same information when visiting our website and, for example, the items remain in your shopping cart until you have paid. We may place these cookies without your consent.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">5.2 Statistics cookies</h3>
                            <p class="text-gray-600 leading-relaxed">We use statistics cookies to optimize the website experience for our users. With these statistics cookies we get insights in the usage of our website. We ask your permission to place statistics cookies.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">5.3 Marketing/Tracking cookies</h3>
                            <p class="text-gray-600 leading-relaxed">Marketing/Tracking cookies are cookies or any other form of local storage, used to create user profiles to display advertising or to track the user on this website or across several websites for similar marketing purposes.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">5.4 Social media</h3>
                            <p class="text-gray-600 leading-relaxed">On our website, we have included content from Instagram and Facebook to promote web pages (e.g. "like", "pin") or share (e.g. "tweet") on social networks like Instagram and Facebook. This content is embedded with code derived from Instagram and Facebook and places cookies. This content might store and process certain information for personalized advertising.</p>
                            <p class="text-gray-600 leading-relaxed mt-2 text-sm italic">Instagram and Facebook are located in the United States.</p>
                        </div>
                    </div>
                </section>

                <!-- 6. Placed cookies -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>6. Placed cookies
                    </h2>

                    <div class="space-y-4">

                        <!-- Elementor -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden" open>
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Elementor <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-medium">Statistics (anonymous)</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use Elementor for content creation.</p>
                                <p><strong>Sharing data:</strong> This data is not shared with third parties.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">elementor</td><td class="p-2">persistent</td><td class="p-2">Store performed actions on the website</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Mailpoet -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>mailpoet <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Marketing</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use mailpoet for mailing list subscriptions.</p>
                                <p><strong>Sharing data:</strong> For more information, please read the mailpoet Privacy Statement.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">mailpoet_subscriber</td><td class="p-2">persistent</td><td class="p-2">Provide subscription service</td></tr>
                                            <tr><td class="p-2 font-medium">mailpoet_page_view</td><td class="p-2">persistent</td><td class="p-2">Provide subscription service</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- LiteSpeed -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>LiteSpeed <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Functional</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use LiteSpeed for website hosting.</p>
                                <p><strong>Sharing data:</strong> This data is not shared with third parties.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">_lscache_vary</td><td class="p-2">2 days</td><td class="p-2">Provide the prevention of cached pages</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- WordPress -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>WordPress <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Functional</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use WordPress for website development.</p>
                                <p><strong>Sharing data:</strong> This data is not shared with third parties.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">wordpress_logged_in_*</td><td class="p-2">persistent</td><td class="p-2">Store logged in users</td></tr>
                                            <tr><td class="p-2 font-medium">wordpress_test_cookie</td><td class="p-2">session</td><td class="p-2">Read if cookies can be placed</td></tr>
                                            <tr><td class="p-2 font-medium">wpEmojiSettingsSupports</td><td class="p-2">session</td><td class="p-2">Store browser details</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Google Fonts -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Google Fonts <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Marketing</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use Google Fonts for display of webfonts.</p>
                                <p><strong>Sharing data:</strong> For more information, please read the Google Fonts Privacy Statement.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">Google Fonts API</td><td class="p-2">expires immediately</td><td class="p-2">Read user IP address</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Google Maps -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Google Maps <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Marketing</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use Google Maps for maps display.</p>
                                <p><strong>Sharing data:</strong> For more information, please read the Google Maps Privacy Statement.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">Google Maps API</td><td class="p-2">expires immediately</td><td class="p-2">Read user IP address</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Facebook -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Facebook <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Marketing, Functional</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use Facebook for display of recent social posts and/or social share buttons.</p>
                                <p><strong>Sharing data:</strong> For more information, please read the Facebook Privacy Statement.</p>
                                <p class="font-semibold mt-3 text-gray-900">Marketing</p>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">_fbc</td><td class="p-2">2 years</td><td class="p-2">Store last visit</td></tr>
                                            <tr><td class="p-2 font-medium">fbm*</td><td class="p-2">1 year</td><td class="p-2">Store account details</td></tr>
                                            <tr><td class="p-2 font-medium">xs</td><td class="p-2">3 months</td><td class="p-2">Store a unique session ID</td></tr>
                                            <tr><td class="p-2 font-medium">fr</td><td class="p-2">3 months</td><td class="p-2">Provide ad delivery or retargeting</td></tr>
                                            <tr><td class="p-2 font-medium">act</td><td class="p-2">90 days</td><td class="p-2">Store logged in users</td></tr>
                                            <tr><td class="p-2 font-medium">_fbp</td><td class="p-2">3 months</td><td class="p-2">Store and track visits across websites</td></tr>
                                            <tr><td class="p-2 font-medium">datr</td><td class="p-2">2 years</td><td class="p-2">Provide fraud prevention</td></tr>
                                            <tr><td class="p-2 font-medium">c_user</td><td class="p-2">30 days</td><td class="p-2">Store a unique user ID</td></tr>
                                            <tr><td class="p-2 font-medium">sb</td><td class="p-2">2 years</td><td class="p-2">Store browser details</td></tr>
                                            <tr><td class="p-2 font-medium">*_fbm_</td><td class="p-2">1 year</td><td class="p-2">Store account details</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="font-semibold mt-3 text-gray-900">Functional</p>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">wd</td><td class="p-2">1 week</td><td class="p-2">Read screen resolution</td></tr>
                                            <tr><td class="p-2 font-medium">csm</td><td class="p-2">90 days</td><td class="p-2">Provide fraud prevention</td></tr>
                                            <tr><td class="p-2 font-medium">actppresence</td><td class="p-2">session</td><td class="p-2">Store and track if the browser tab is active</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Complianz -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Complianz <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Functional</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Usage:</strong> We use Complianz for cookie consent management.</p>
                                <p><strong>Sharing data:</strong> This data is not shared with third parties.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th><th class="text-left p-2">Function</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">cmplz_functional</td><td class="p-2">365 days</td><td class="p-2">Store cookie consent preferences</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_statistics</td><td class="p-2">365 days</td><td class="p-2">Store cookie consent preferences</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_preferences</td><td class="p-2">365 days</td><td class="p-2">Store cookie consent preferences</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_marketing</td><td class="p-2">365 days</td><td class="p-2">Store cookie consent preferences</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                        <!-- Miscellaneous -->
                        <details class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <summary class="cursor-pointer p-4 font-bold text-gray-900 flex items-center justify-between">
                                <span>Miscellaneous <span class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full font-medium">Purpose pending investigation</span></span>
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 space-y-2">
                                <p><strong>Sharing data:</strong> Sharing of data is pending investigation.</p>
                                <div class="overflow-x-auto mt-3">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-600 uppercase">
                                            <tr><th class="text-left p-2">Name</th><th class="text-left p-2">Expiration</th></tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr><td class="p-2 font-medium">__mpq_150605b3b9f979922f2ac5a52e2dcfe9_ev</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">popup_form_dismissed_2</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">__mp_opt_in_out_150605b3b9f979922f2ac5a52e2dcfe9</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">_grecaptcha</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">mp_tab_id_elementor-builder-editor_150605b3b9f979922f2ac5a52e2dcfe9</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">mp_gen_new_tab_id_elementor-builder-editor_150605b3b9f979922f2ac5a52e2dcfe9</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">_ga</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_consented_services</td><td class="p-2">365 days</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_policy_id</td><td class="p-2">365 days</td></tr>
                                            <tr><td class="p-2 font-medium">cmplz_banner-status</td><td class="p-2">365 days</td></tr>
                                            <tr><td class="p-2 font-medium">wp-settings-2</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">wp-settings-time-2</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">_ga_TXH6EE2KPV</td><td class="p-2">—</td></tr>
                                            <tr><td class="p-2 font-medium">__mprec_elementor-builder-editor_150605b3b9f979922f2ac5a52e2dcfe9_66e67866-3c58-4016-8160-714e530695</td><td class="p-2">—</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </details>

                    </div>
                </section>

                <!-- 7. Consent -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>7. Consent
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">When you visit our website for the first time, we will show you a pop-up with an explanation about cookies. As soon as you click on "Save preferences", you consent to us using the categories of cookies and plug-ins you selected in the pop-up, as described in this Cookie Policy. You can disable the use of cookies via your browser, but please note that our website may no longer work properly.</p>

                    <h3 class="text-lg font-bold text-gray-900 mb-4">7.1 Manage your consent settings</h3>

                    <div class="space-y-3">
                        <div class="flex items-start p-4 bg-green-50 border border-green-100 rounded-lg">
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold mr-3 mt-0.5 shrink-0">ALWAYS ACTIVE</span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Functional</p>
                                <p class="text-xs text-gray-600 mt-1">The technical storage or access is strictly necessary for the legitimate purpose of enabling the use of a specific service explicitly requested by the subscriber or user, or for the sole purpose of carrying out the transmission of a communication over an electronic communications network.</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full font-bold mr-3 mt-0.5 shrink-0">OPTIONAL</span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Statistics</p>
                                <p class="text-xs text-gray-600 mt-1">The technical storage or access that is used exclusively for statistical purposes.</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full font-bold mr-3 mt-0.5 shrink-0">OPTIONAL</span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">Marketing</p>
                                <p class="text-xs text-gray-600 mt-1">The technical storage or access is required to create user profiles to send advertising, or to track the user on a website or across several websites for similar marketing purposes.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 8. Enabling/disabling and deleting cookies -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>8. Enabling/disabling and deleting cookies
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">You can use your internet browser to automatically or manually delete cookies. You can also specify that certain cookies may not be placed. Another option is to change the settings of your internet browser so that you receive a message each time a cookie is placed. For more information about these options, please refer to the instructions in the Help section of your browser.</p>
                    <p class="text-gray-600 leading-relaxed">Please note that our website may not work properly if all cookies are disabled. If you do delete the cookies in your browser, they will be placed again after your consent when you visit our website again.</p>
                </section>

                <!-- 9. Your rights -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>9. Your rights with respect to personal data
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">You have the following rights with respect to your personal data:</p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>You have the right to know why your personal data is needed, what will happen to it, and how long it will be retained for.</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Right of access:</strong> You have the right to access your personal data that is known to us.</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Right of rectification:</strong> you have the right to supplement, correct, have deleted or blocked your personal data whenever you wish.</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>If you give us your consent to process your data, you have the right to revoke that consent and to have your personal data deleted.</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Right to transfer your data:</strong> You have the right to request all your personal data from the controller and transfer it in its entirety to another controller.</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Right to object:</strong> you may object to the processing of your data. We comply with this, unless there are justified grounds for processing.</span></li>
                    </ul>
                    <p class="text-gray-600 leading-relaxed mt-4">To exercise these rights, please contact us. Please refer to the contact details at the bottom of this Cookie Policy. If you have a complaint about how we handle your data, we would like to hear from you, but you also have the right to submit a complaint to the supervisory authority (the Information Commissioner's Office (ICO)).</p>
                </section>

                <!-- 10. Contact details -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>10. Contact details
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">For questions and/or comments about our Cookie Policy and this statement, please contact us by using the following contact details:</p>
                    <div class="bg-primary-50 border border-primary-100 rounded-xl p-5 text-sm">
                        <p class="font-bold text-gray-900">Slough SEND Parent Carer Forum</p>
                        <p class="text-gray-600 mt-1">Slough, United Kingdom</p>
                        <p class="text-gray-600 mt-2">Website: <a href="https://sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">https://sloughsendpcf.org</a></p>
                        <p class="text-gray-600">Email: <a href="mailto:info@sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">info@sloughsendpcf.org</a></p>
                    </div>
                    <p class="text-xs text-gray-400 mt-4 italic">This Cookie Policy was synchronised with cookiedatabase.org on July 26, 2026.</p>
                </section>

            </div>
        </div>
    </section>
</div>
