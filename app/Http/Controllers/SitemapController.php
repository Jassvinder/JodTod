<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap with static pages and published blog posts.
     */
    public function index(): Response
    {
        $baseUrl = config('app.url');

        // Static pages with their priorities and change frequencies
        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/features', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/about', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/privacy', 'priority' => '0.4', 'changefreq' => 'yearly'],
            ['url' => '/terms', 'priority' => '0.4', 'changefreq' => 'yearly'],
            ['url' => '/tools/expense-splitter', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/blog', 'priority' => '0.9', 'changefreq' => 'daily'],
        ];

        // Published blog posts
        $blogPosts = BlogPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add static pages
        foreach ($staticPages as $page) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . $page['url'] . '</loc>' . "\n";
            $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        // Add blog posts
        foreach ($blogPosts as $post) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/blog/' . $post->slug . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $post->updated_at->toW3cString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
