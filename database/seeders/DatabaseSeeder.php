<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin default
        User::create([
            'name'     => 'Admin Pasarin',
            'email'    => 'admin@pasarin.test',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Akun customer contoh
        User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@pasarin.test',
            'password' => Hash::make('password123'),
            'role'     => 'customer',
        ]);

        $sembako = Category::create(['name' => 'Sembako']);
        $minuman = Category::create(['name' => 'Minuman']);
        $snack   = Category::create(['name' => 'Snack']);

        $products = [
            ['name' => 'Beras Premium 5kg', 'category_id' => $sembako->id, 'price' => 75000, 'stock' => 40],
            ['name' => 'Minyak Goreng 2L', 'category_id' => $sembako->id, 'price' => 32000, 'stock' => 60],
            ['name' => 'Gula Pasir 1kg', 'category_id' => $sembako->id, 'price' => 15000, 'stock' => 80],
            ['name' => 'Teh Botol 450ml', 'category_id' => $minuman->id, 'price' => 5000, 'stock' => 120],
            ['name' => 'Kopi Susu Kaleng', 'category_id' => $minuman->id, 'price' => 8000, 'stock' => 90],
            ['name' => 'Air Mineral 600ml', 'category_id' => $minuman->id, 'price' => 4000, 'stock' => 150],
            ['name' => 'Keripik Singkong', 'category_id' => $snack->id, 'price' => 12000, 'stock' => 55],
            ['name' => 'Wafer Coklat', 'category_id' => $snack->id, 'price' => 9500, 'stock' => 70],
        ];

        foreach ($products as $p) {
            Product::create([
                'category_id' => $p['category_id'],
                'name'        => $p['name'],
                'description' => 'Produk berkualitas dengan harga terjangkau.',
                'price'       => $p['price'],
                'stock'       => $p['stock'],
                'image'       => 'dummy-product.png',
                'is_active'   => true,
            ]);
        }
    }
}
