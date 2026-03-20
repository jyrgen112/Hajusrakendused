<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Mechanical Keyboard', 'description' => 'RGB backlit mechanical keyboard with Cherry MX switches.', 'price' => 89.99, 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400'],
            ['name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse with long battery life.', 'price' => 39.99, 'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400'],
            ['name' => 'USB-C Hub', 'description' => '7-in-1 USB-C hub with HDMI, USB 3.0 and SD card reader.', 'price' => 49.99, 'image' => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400'],
            ['name' => 'Monitor Stand', 'description' => 'Adjustable aluminium monitor stand with storage drawer.', 'price' => 59.99, 'image' => 'https://images.unsplash.com/photo-1585792180666-f7347c490ee2?w=400'],
            ['name' => 'Webcam HD', 'description' => '1080p HD webcam with built-in microphone.', 'price' => 69.99, 'image' => 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400'],
            ['name' => 'Desk Lamp', 'description' => 'LED desk lamp with adjustable brightness and USB charging port.', 'price' => 34.99, 'image' => 'https://images.unsplash.com/photo-1505409628601-edc9af17fda6?w=400'],
            ['name' => 'Laptop Stand', 'description' => 'Portable foldable laptop stand, compatible with all laptops.', 'price' => 29.99, 'image' => 'https://images.unsplash.com/photo-1611186871525-61e12f87b8a1?w=400'],
            ['name' => 'Noise Cancelling Headphones', 'description' => 'Over-ear headphones with active noise cancellation.', 'price' => 129.99, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400'],
            ['name' => 'Desk Mat', 'description' => 'Large leather desk mat to protect your workspace.', 'price' => 24.99, 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400'],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, ['stock' => 100]));
        }
    }
}