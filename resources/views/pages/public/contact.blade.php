@extends('layouts.public')

@section('title', 'Contact Us - JodTod')
@section('meta_description', 'Get in touch with the JodTod team. We\'d love to hear from you — questions, feedback, or just to say hello.')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight">Contact Us</h1>
            <p class="mt-6 text-lg sm:text-xl text-primary-100">
                Have a question or feedback? We'd love to hear from you.
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

            <!-- Contact Information -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Get in Touch</h2>
                <p class="mt-4 text-gray-600">
                    Whether you have a question about features, need help with your account, or just want to share feedback —
                    our team is ready to help.
                </p>

                <div class="mt-8 space-y-6">
                    <!-- Email -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                            <p class="mt-1 text-gray-600">
                                <a href="mailto:support@jodtod.com" class="text-primary-600 hover:text-primary-700">support@jodtod.com</a>
                            </p>
                            <p class="mt-1 text-sm text-gray-500">We'll get back to you within 24 hours.</p>
                        </div>
                    </div>

                    <!-- Response Time -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Response Time</h3>
                            <p class="mt-1 text-gray-600">We typically respond within 24 hours on business days.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form (UI Only - Placeholder) -->
            <div class="bg-gray-50 rounded-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900">Send us a Message</h2>
                <p class="mt-2 text-sm text-gray-500">Fill out the form below and we'll get back to you soon.</p>

                <form class="mt-6 space-y-5" onsubmit="event.preventDefault(); alert('Thank you! This form is a placeholder. Please email us at support@jodtod.com');">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Your name"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="you@example.com"
                        >
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="How can we help?"
                        >
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Tell us more about your question or feedback..."
                        ></textarea>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors"
                    >
                        Send Message
                    </button>

                    <p class="text-xs text-gray-500 text-center">We'll get back to you within 24 hours.</p>
                </form>
            </div>

        </div>
    </div>
</section>
@endsection
