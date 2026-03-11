<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<h2>About JodTod</h2><p>JodTod is a smart expense tracker and splitter app that helps you manage personal expenses and split group expenses with friends, family, and roommates.</p><h3>Our Mission</h3><p>We believe managing money with friends shouldn\'t be awkward. JodTod makes it simple to track who owes what, settle up fairly, and keep friendships strong.</p><h3>What We Offer</h3><ul><li><strong>Personal Expense Tracking</strong> - Track your daily spending with categories and charts</li><li><strong>Group Expense Splitting</strong> - Split bills equally, by custom amounts, or by percentage</li><li><strong>Smart Settlement</strong> - Our algorithm minimizes the number of transactions needed</li><li><strong>Real-time Notifications</strong> - Stay updated on group activities</li></ul>',
                'meta_title' => 'About JodTod - Expense Tracker & Splitter',
                'meta_description' => 'Learn about JodTod, a smart expense tracker and splitter app for managing personal and group expenses easily.',
                'is_published' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h2>Get in Touch</h2><p>Have questions, feedback, or need help? We\'d love to hear from you!</p><h3>Email</h3><p>support@jodtod.com</p><h3>Social Media</h3><p>Follow us on social media for updates and tips:</p><ul><li>Twitter: @jodtod</li><li>Instagram: @jodtod.app</li></ul><h3>Business Inquiries</h3><p>For partnerships and business inquiries, please email: hello@jodtod.com</p>',
                'meta_title' => 'Contact JodTod - Get in Touch',
                'meta_description' => 'Contact the JodTod team for support, feedback, or business inquiries.',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'content' => '<h2>Privacy Policy</h2><p><strong>Last updated:</strong> March 2026</p><h3>Information We Collect</h3><p>We collect information you provide directly, including your name, email, phone number, and expense data.</p><h3>How We Use Your Information</h3><ul><li>To provide and maintain our service</li><li>To notify you about changes to our service</li><li>To provide customer support</li><li>To gather analysis so we can improve our service</li></ul><h3>Data Security</h3><p>We implement appropriate security measures to protect your personal information. Your data is encrypted in transit and at rest.</p><h3>Contact Us</h3><p>If you have questions about this Privacy Policy, please contact us at support@jodtod.com.</p>',
                'meta_title' => 'Privacy Policy - JodTod',
                'meta_description' => 'Read JodTod\'s privacy policy to understand how we collect, use, and protect your data.',
                'is_published' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms',
                'content' => '<h2>Terms of Service</h2><p><strong>Last updated:</strong> March 2026</p><h3>Acceptance of Terms</h3><p>By accessing or using JodTod, you agree to be bound by these Terms of Service.</p><h3>Use of Service</h3><p>You may use JodTod for lawful purposes only. You are responsible for maintaining the security of your account.</p><h3>User Content</h3><p>You retain ownership of all data you submit. We do not sell your personal data to third parties.</p><h3>Limitation of Liability</h3><p>JodTod is provided "as is" without warranties. We are not liable for any financial decisions made based on the app\'s calculations.</p><h3>Changes to Terms</h3><p>We may update these terms from time to time. Continued use of the service constitutes acceptance of the updated terms.</p>',
                'meta_title' => 'Terms of Service - JodTod',
                'meta_description' => 'Read the Terms of Service for using JodTod expense tracker and splitter app.',
                'is_published' => true,
            ],
            [
                'title' => 'Features',
                'slug' => 'features',
                'content' => '<h2>Features</h2><h3>Personal Expense Tracking</h3><p>Track every rupee with categories, date filters, and visual charts. Know exactly where your money goes each month.</p><h3>Group Expense Splitting</h3><p>Split bills with friends using equal, custom, or percentage splits. Perfect for trips, roommates, and dining out.</p><h3>Smart Settlement</h3><p>Our algorithm calculates the minimum number of transactions needed to settle all debts. No more confusing IOUs.</p><h3>Real-time Notifications</h3><p>Get notified instantly when expenses are added, settlements are requested, or you\'re added to a group.</p><h3>Dashboard Overview</h3><p>See your financial summary at a glance — monthly spending, group balances, and recent activity all in one place.</p><h3>Works Everywhere</h3><p>JodTod is a Progressive Web App. Install it on your phone, tablet, or use it on desktop. Works offline too.</p>',
                'meta_title' => 'Features - JodTod Expense Tracker',
                'meta_description' => 'Explore JodTod features: personal expense tracking, group splitting, smart settlements, notifications, and more.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
