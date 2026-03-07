<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = config('site.categories');

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['icon' => $category['icon']]
            );
        }
    }
}
