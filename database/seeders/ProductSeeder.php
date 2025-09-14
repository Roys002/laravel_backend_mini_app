<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create(['name' => 'Laptop', 'price' => 1200.00, 'description' => 'Powerful laptop']);
        Product::create(['name' => 'Phone', 'price' => 600.00, 'description' => 'Smartphone']);
    }
}
