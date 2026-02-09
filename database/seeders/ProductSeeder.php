<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro',
                'description' => 'High-performance laptop for professionals',
                'price' => 1299.99,
                'quantity' => 15,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with long battery life',
                'price' => 29.99,
                'quantity' => 100,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical keyboard with blue switches',
                'price' => 89.99,
                'quantity' => 30,
            ],
            [
                'name' => '4K Monitor',
                'description' => '27-inch 4K UHD monitor with HDR',
                'price' => 399.99,
                'quantity' => 25,
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with 4K HDMI',
                'price' => 49.99,
                'quantity' => 75,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}