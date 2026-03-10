@extends('layouts.public')

@section('title', 'Terms of Service - JodTod')
@section('meta_description', 'JodTod Terms of Service - Read our terms and conditions for using the JodTod expense tracking and splitting application.')

@section('content')
<!-- Hero Section -->
<section class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Terms of Service</h1>
        <p class="mt-4 text-gray-600">Last updated: March 10, 2026</p>
    </div>
</section>

<!-- Content -->
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none text-gray-600">

            <p>
                Welcome to JodTod. By accessing or using our application, you agree to be bound by these Terms of Service.
                Please read them carefully.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Acceptance of Terms</h2>
            <p>
                By creating an account or using JodTod, you agree to these Terms of Service and our Privacy Policy.
                If you do not agree to these terms, please do not use our application.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. Account Registration</h2>
            <p>To use JodTod, you must:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Be at least 13 years old.</li>
                <li>Provide accurate and complete registration information.</li>
                <li>Keep your account credentials secure and confidential.</li>
                <li>Notify us immediately of any unauthorized use of your account.</li>
            </ul>
            <p class="mt-3">
                You are responsible for all activity that occurs under your account.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. Acceptable Use</h2>
            <p>You agree not to:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Use JodTod for any unlawful purpose or in violation of any laws.</li>
                <li>Attempt to gain unauthorized access to our systems or other users' accounts.</li>
                <li>Interfere with or disrupt the application or its infrastructure.</li>
                <li>Upload malicious content, spam, or harmful data.</li>
                <li>Impersonate another person or entity.</li>
                <li>Use the application to harass, abuse, or harm others.</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. User Content</h2>
            <p>
                You retain ownership of the data you enter into JodTod (expenses, group names, descriptions, etc.).
                By using our application, you grant us a limited license to store, process, and display your data
                as necessary to provide our services.
            </p>
            <p class="mt-3">
                You are responsible for the accuracy of the expense data you enter. JodTod is a tracking tool and
                does not verify the accuracy of financial information.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Pricing & Payments</h2>
            <p>
                JodTod is currently <strong>free to use</strong>. We may introduce premium features in the future.
                If we do, we will provide advance notice and any paid features will be clearly marked. Free features
                available at the time of your registration will remain free for your account.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Settlement Disclaimer</h2>
            <p>
                JodTod calculates suggested settlements based on the expense data entered by users. These calculations
                are for informational purposes only. JodTod does not process, facilitate, or guarantee any actual
                financial transactions between users. Settlement of debts is entirely between the parties involved.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Termination</h2>
            <p>
                You may delete your account at any time from your profile settings. Upon deletion, your personal data
                will be removed within 30 days.
            </p>
            <p class="mt-3">
                We reserve the right to suspend or terminate your account if you violate these terms, engage in abusive
                behavior, or if required by law.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Limitation of Liability</h2>
            <p>
                JodTod is provided "as is" and "as available" without warranties of any kind, either express or implied.
                We do not guarantee that the application will be uninterrupted, error-free, or completely secure.
            </p>
            <p class="mt-3">
                To the maximum extent permitted by law, JodTod and its creators shall not be liable for any indirect,
                incidental, special, consequential, or punitive damages, including but not limited to loss of data,
                loss of profits, or financial disputes between users.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">9. Changes to Terms</h2>
            <p>
                We may update these Terms of Service from time to time. We will notify you of significant changes
                by posting a notice in the application or sending an email. Your continued use of JodTod after
                changes are posted constitutes acceptance of the updated terms.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">10. Governing Law</h2>
            <p>
                These Terms of Service shall be governed by and construed in accordance with the laws of India.
                Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts
                in India.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">11. Contact Us</h2>
            <p>
                If you have any questions about these Terms of Service, please contact us at:
            </p>
            <p class="mt-2">
                <strong>Email:</strong>
                <a href="mailto:support@jodtod.com" class="text-primary-600 hover:text-primary-700">support@jodtod.com</a>
            </p>

        </div>
    </div>
</section>
@endsection
