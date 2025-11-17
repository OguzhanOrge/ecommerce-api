<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate();

        $categories = Category::all();

        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'name' => $category->name . ' Product ' . $i,
                    'description' => 'Description for ' . $category->name . ' Product ' . $i,
                    'price' => rand(10, 500),
                    'stock_quantity' => rand(5, 100),
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
