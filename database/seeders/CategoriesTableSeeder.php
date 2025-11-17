<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::truncate();

        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic items like phones, laptops, etc.'],
            ['name' => 'Books', 'description' => 'All kinds of books and literature.'],
            ['name' => 'Clothing', 'description' => 'Men and women clothing items.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
