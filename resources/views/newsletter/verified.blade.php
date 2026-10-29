<x-layouts.master>
    <section class="py-20 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">
                
                <!-- Icon -->
                <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">You're Verified!</h1>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">Thank you for verifying your email address. You are now officially subscribed to the Slough SEND Parent Carer Forum newsletter.</p>
                
                <a href="{{ route('welcome') }}" class="inline-flex justify-center items-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-300">
                    Return Home
                </a>
            </div>
        </div>
    </section>
</x-layouts.master>