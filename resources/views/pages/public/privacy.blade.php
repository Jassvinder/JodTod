@extends('layouts.public')

@section('title', 'Privacy Policy - JodTod')
@section('meta_description', 'JodTod Privacy Policy - Learn how we collect, use, and protect your personal information.')

@section('content')
<!-- Hero Section -->
<section class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Privacy Policy</h1>
        <p class="mt-4 text-gray-600">Last updated: March 10, 2026</p>
    </div>
</section>

<!-- Content -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none text-gray-600">

            <p>
                At JodTod, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose,
                and safeguard your information when you use our expense tracking and splitting application.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Information We Collect</h2>

            <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Personal Information</h3>
            <p>When you create an account, we may collect:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Name</li>
                <li>Email address</li>
                <li>Phone number (for group features)</li>
                <li>Profile picture (optional)</li>
            </ul>

            <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Usage Information</h3>
            <p>When you use JodTod, we collect:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Expense data you enter (amounts, categories, descriptions)</li>
                <li>Group membership and expense sharing information</li>
                <li>Device information and browser type</li>
                <li>Usage patterns and feature interactions</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Provide, operate, and maintain the JodTod application</li>
                <li>Process and track your expenses and group settlements</li>
                <li>Send you notifications related to your account and group activities</li>
                <li>Improve our application and develop new features</li>
                <li>Communicate with you about updates, support, and promotions</li>
                <li>Detect and prevent fraud or abuse</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. Data Sharing</h2>
            <p>We do not sell your personal information. We may share your information only in the following cases:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li><strong>With group members:</strong> Your name and shared expense data is visible to members of groups you join.</li>
                <li><strong>Service providers:</strong> We may share data with trusted third-party service providers who help us operate our application (hosting, analytics).</li>
                <li><strong>Legal requirements:</strong> We may disclose information if required by law or to protect our rights.</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. Cookies</h2>
            <p>
                JodTod uses cookies and similar technologies to maintain your session, remember your preferences,
                and understand how you use our application. These are essential cookies required for the application to function properly.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Data Security</h2>
            <p>
                We implement appropriate technical and organizational security measures to protect your personal information,
                including encryption of data in transit and at rest, secure authentication mechanisms, and regular security audits.
                However, no method of transmission over the Internet is 100% secure, and we cannot guarantee absolute security.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Your Rights</h2>
            <p>You have the right to:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li><strong>Access:</strong> Request a copy of the personal data we hold about you.</li>
                <li><strong>Correction:</strong> Request correction of inaccurate personal data.</li>
                <li><strong>Deletion:</strong> Request deletion of your account and associated data.</li>
                <li><strong>Export:</strong> Request a copy of your data in a portable format.</li>
                <li><strong>Opt-out:</strong> Unsubscribe from promotional communications at any time.</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Data Retention</h2>
            <p>
                We retain your personal information for as long as your account is active or as needed to provide you services.
                If you delete your account, we will delete your personal information within 30 days, except where we are required
                to retain it for legal purposes.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Children's Privacy</h2>
            <p>
                JodTod is not intended for children under 13 years of age. We do not knowingly collect personal information
                from children under 13. If we discover that we have collected information from a child under 13, we will
                delete it promptly.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">9. Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new
                Privacy Policy on this page and updating the "Last updated" date. We encourage you to review this policy periodically.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">10. Contact Us</h2>
            <p>
                If you have any questions about this Privacy Policy, please contact us at:
            </p>
            <p class="mt-2">
                <strong>Email:</strong>
                <a href="mailto:support@jodtod.com" class="text-primary-600 hover:text-primary-700">support@jodtod.com</a>
            </p>

        </div>
    </div>
</section>
@endsection
