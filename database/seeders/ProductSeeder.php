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
                'category' => 'Electronics',
                'description' => 'High-performance laptop for professionals',
                'price' => 1299.99,
                'quantity' => 15,
                'minimum_stock' => 5,
                'status' => 'Active',
            ],
            [
                'name' => 'Wireless Mouse',
                'category' => 'Accessories',
                'description' => 'Ergonomic wireless mouse with long battery life',
                'price' => 29.99,
                'quantity' => 100,
                'minimum_stock' => 20,
                'status' => 'Active',
            ],
            [
                'name' => 'Mechanical Keyboard',
                'category' => 'Accessories',
                'description' => 'RGB mechanical keyboard with blue switches',
                'price' => 89.99,
                'quantity' => 30,
                'minimum_stock' => 10,
                'status' => 'Active',
            ],
            [
                'name' => '4K Monitor',
                'category' => 'Electronics',
                'description' => '27-inch 4K UHD monitor with HDR',
                'price' => 399.99,
                'quantity' => 25,
                'minimum_stock' => 8,
                'status' => 'Active',
            ],
            [
                'name' => 'USB-C Hub',
                'category' => 'Accessories',
                'description' => '7-in-1 USB-C hub with 4K HDMI',
                'price' => 49.99,
                'quantity' => 4,
                'minimum_stock' => 10,
                'status' => 'Active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}