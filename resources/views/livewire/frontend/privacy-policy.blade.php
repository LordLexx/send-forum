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
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">Privacy Policy</h1>
            <p class="text-lg text-primary-50 max-w-2xl mx-auto">Last updated: April 14th, 2026</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 space-y-10">

                <!-- Who we are -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Who we are
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>Our website address is: <a href="https://sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">https://sloughsendpcf.org</a>.</p>
                        <p>Slough SEND Parent Carer Forum is a Slough (UK) based parent/carer advocacy group supporting families of children with Special Educational Needs and Disabilities (SEND).</p>
                        <p>We are the data controller for the personal data we process. For any questions about this policy or your data, please contact us at <a href="mailto:info@sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">info@sloughsendpcf.org</a>.</p>
                        <p>If you are unhappy with how we handle your data, you can complain to the Information Commissioner's Office (ICO) at <a href="https://www.ico.org.uk/concerns" class="text-primary-600 hover:text-primary-800 underline">https://www.ico.org.uk/concerns</a> or by calling 0303 123 1113.</p>
                    </div>
                </section>

                <!-- What personal data we collect -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>What personal data we collect and why we collect it
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>We collect personal data that you provide directly to us, including:</p>
                        <ul class="list-disc pl-6 space-y-1">
                            <li>Name, email address, phone number, and postal address (if provided)</li>
                            <li>Information submitted via contact or enquiry forms</li>
                            <li>Newsletter subscription details and preferences</li>
                        </ul>
                        <p>You may choose to share information about your family situation or SEND-related concerns. We do not directly collect or target children's personal data.</p>
                        <p>We may also collect technical data such as IP address, browser type, and device information for website functionality and security.</p>
                    </div>
                </section>

                <!-- Comments -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Comments
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor's IP address and browser user agent string to help spam detection.</p>
                        <p>An anonymised string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: <a href="https://automattic.com/privacy/" class="text-primary-600 hover:text-primary-800 underline">https://automattic.com/privacy/</a>.</p>
                        <p>After approval of your comment, your profile picture is visible to the public in the context of your comment.</p>
                    </div>
                </section>

                <!-- Media -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Media
                    </h2>
                    <p class="text-gray-600 leading-relaxed">If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors can download and extract any location data from images on the website.</p>
                </section>

                <!-- Contact forms and enquiries -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Contact forms and enquiries
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>When you contact us via forms or email, we collect the information you provide in order to:</p>
                        <ul class="list-disc pl-6 space-y-1">
                            <li>Respond to your query</li>
                            <li>Provide advocacy support</li>
                            <li>Direct you to relevant services or resources</li>
                        </ul>
                        <p>If you include sensitive information (such as health or disability details), we will only use it as necessary to respond to your enquiry and in accordance with UK GDPR.</p>
                    </div>
                </section>

                <!-- Newsletter -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Newsletter
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>If you sign up to our newsletter, we will use your email address to send updates about SEND-related news, events, and resources.</p>
                        <p>We only send newsletters where you have given your consent. You can unsubscribe at any time using the link in our emails or by contacting us.</p>
                    </div>
                </section>

                <!-- AI Chatbot -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-orange-500 rounded-full mr-3"></span>AI Chatbot
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">Our website uses an AI-powered chatbot to help visitors find information, access support and navigate our resources more easily.</p>

                    <div class="space-y-5">
                        <div class="pl-4 border-l-2 border-orange-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">How the Chatbot Works</h3>
                            <p class="text-gray-600 leading-relaxed">The chatbot involves a website-integrated section which connects to large language models such as OpenAI's GPT in order to generate responses to your questions and signpost information.</p>
                            <p class="text-gray-600 leading-relaxed mt-2">When you interact with the chatbot, your messages are sent to the large language model provider's servers in order to generate a response. This means the content of your conversation may be processed by the provider as a part of this service.</p>
                        </div>

                        <div class="pl-4 border-l-2 border-orange-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">What data the Chatbot May Process</h3>
                            <p class="text-gray-600 leading-relaxed mb-2">The chatbot and large language model provider may process the following information:</p>
                            <ul class="list-disc pl-6 space-y-1 text-gray-600">
                                <li>The text of messages you type into the chat</li>
                                <li>Basic technical information such as your browser type, IP address and what section of the website you are viewing.</li>
                                <li>Any personal or sensitive details you choose to share voluntarily within the conversation</li>
                            </ul>
                            <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 mt-3 text-sm text-orange-800">
                                <p>We advise you to <strong>not share sensitive personal information</strong> (such as full names, dates of birth, NHS numbers or detailed health or disability information) within the chatbot.</p>
                                <p class="mt-2">If you need to discuss sensitive matters please reach out to appropriate resources or contact parent/carer peers here at the forum at <a href="mailto:info@sloughsendpcf.org" class="underline font-medium">info@sloughsendpcf.org</a></p>
                            </div>
                        </div>

                        <div class="pl-4 border-l-2 border-orange-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Chatbot Data Retention and Third-Party Processing</h3>
                            <div class="text-gray-600 leading-relaxed space-y-2">
                                <p>Chatbot conversations may be stored by us for the purpose of improving the service and reviewing chatbot performance. We will review and delete stored conversation logs in line with our general data retention principles.</p>
                                <p>OpenAI processes chat inputs on our behalf. OpenAI's data handling is governed by their own privacy policy and data processing terms. By default, OpenAI does not use data submitted via the API to train their models. For more information, see: <a href="https://openai.com/policies/privacy-policy" class="text-primary-600 hover:text-primary-800 underline">https://openai.com/policies/privacy-policy</a>.</p>
                                <p>Your chatbot interaction data is not sold, rented, or used for advertising purposes.</p>
                            </div>
                        </div>

                        <div class="pl-4 border-l-2 border-orange-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Legal Basis for Chatbot Processing</h3>
                            <p class="text-gray-600 leading-relaxed">We process chatbot conversation data on the basis of legitimate interests; specifically to provide an accessible, helpful information service to parents and carers visiting our site. Where you voluntarily share sensitive information, we rely on your explicit consent (by virtue of you choosing to submit that information).</p>
                        </div>

                        <div class="pl-4 border-l-2 border-orange-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Your Rights Regarding Chatbot Data</h3>
                            <p class="text-gray-600 leading-relaxed">You have the right to request access to, correction of, or deletion of any personal data processed through the chatbot. To exercise these rights, please contact us at <a href="mailto:info@sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">info@sloughsendpcf.org</a></p>
                        </div>
                    </div>
                </section>

                <!-- Cookies -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Cookies
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience and last for one year.</p>
                        <p>If you visit our login page, a temporary cookie will be set to determine if your browser accepts cookies. This contains no personal data and is discarded when you close your browser.</p>
                        <p>When you log in, cookies will store your login information and screen preferences. Login cookies last for two days, and screen options cookies last for a year. Selecting "Remember Me" extends login to two weeks.</p>
                        <p>If you edit or publish an article, a cookie will be saved indicating the post ID. It expires after one day.</p>
                        <p>We may also use essential cookies for website functionality. We do not use non-essential cookies for tracking or advertising without consent.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('cookie.policy') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800">
                            Read our full Cookie Policy
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </section>

                <!-- Embedded content -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Embedded content from other websites
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>Articles on this site may include embedded content (e.g. videos, images, articles). Embedded content behaves as if you visited the external website directly.</p>
                        <p>These websites may collect data about you, use cookies, and monitor your interaction with that content.</p>
                    </div>
                </section>

                <!-- Who we share your data with -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Who we share your data with
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>We do not sell or rent your data.</p>
                        <p>We may share your data with:</p>
                        <ul class="list-disc pl-6 space-y-1">
                            <li>Trusted service providers (e.g. hosting, email, or form services) who process data on our behalf</li>
                            <li>Authorities where required by law (e.g. safeguarding concerns or legal obligations)</li>
                            <li>OpenAI, for the purpose of powering our AI chatbot (see AI Chatbot section above)</li>
                        </ul>
                        <p>All providers are required to comply with UK GDPR and only process data under our instructions.</p>
                    </div>
                </section>

                <!-- How long we retain your data -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>How long we retain your data
                    </h2>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Comments and their metadata are retained indefinitely</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>User account data is stored while the account exists</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Enquiry data is kept for up to 2 years after resolution</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Newsletter data is retained until you unsubscribe or become inactive</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Chatbot conversation logs are retained for a limited period and reviewed periodically for deletion</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Technical logs may be kept for up to 1 year</span></li>
                    </ul>
                    <p class="text-gray-600 leading-relaxed mt-4">We securely delete or anonymise data when no longer needed.</p>
                </section>

                <!-- What rights you have -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>What rights you have over your data
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">If you have an account or have contacted us, you can:</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Request a copy of your personal data</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Request correction of inaccurate data</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Request deletion of your data</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Object to certain types of processing</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Withdraw consent (e.g. for newsletters)</span></li>
                    </ul>
                    <p class="text-gray-600 leading-relaxed mt-4">To exercise your rights, contact <a href="mailto:info@sloughsendpcf.org" class="text-primary-600 hover:text-primary-800 underline">info@sloughsendpcf.org</a>.</p>
                </section>

                <!-- Where your data is sent -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Where your data is sent
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>Visitor comments may be checked through an automated spam detection service.</p>
                        <p>Chatbot messages are processed by OpenAI's servers, which may be located outside the UK/EEA. OpenAI operates under appropriate data transfer safeguards including Standard Contractual Clauses.</p>
                        <p>Your data may be processed by service providers within the UK or EEA. We ensure appropriate safeguards are in place for any data transfers.</p>
                    </div>
                </section>

                <!-- How we protect your data -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>How we protect your data
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">We use appropriate security measures, including:</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Secure servers and encrypted connections (SSL/TLS)</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Restricted access to authorised individuals only</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>Regular data protection reviews</span></li>
                    </ul>
                    <p class="text-gray-600 leading-relaxed mt-4">While no system is completely secure, we take all reasonable steps to protect your data.</p>
                </section>

                <!-- Children's Data -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Children's Data
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-3">
                        <p>Our services are intended for parents and carers. We do not knowingly collect data directly from children under 18.</p>
                        <p>Any information about children is provided by adults and used only for advocacy purposes, with appropriate safeguards.</p>
                        <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 text-sm text-orange-800">
                            <p>We ask that parents and carers take care not to share identifiable information about their children (such as full names or NHS numbers) through the AI chatbot. If you wish to discuss a case in detail with other parent/carers or the forum, please contact us directly.</p>
                        </div>
                    </div>
                </section>

                <!-- Changes to this policy -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Changes to this policy
                    </h2>
                    <p class="text-gray-600 leading-relaxed">We may update this policy from time to time. Updates will be posted on this page with a revised date.</p>
                </section>

                <!-- Legal basis for processing -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 bg-primary-500 rounded-full mr-3"></span>Legal basis for processing
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">We process your data under the following legal bases:</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Consent</strong> (e.g. newsletters)</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Legitimate interests</strong> (e.g. responding to enquiries, running our services, operating the AI chatbot)</span></li>
                        <li class="flex items-start"><svg class="h-5 w-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span><strong>Legal obligations</strong> where applicable</span></li>
                    </ul>
                </section>

            </div>
        </div>
    </section>
</div>
