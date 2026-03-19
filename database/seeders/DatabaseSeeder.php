<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Sample user ───────────────────────────────────────────────────
        $user = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'Demo Owner',
                'password' => Hash::make('password'),
            ]
        );

        // ── Sample shop ───────────────────────────────────────────────────
        Shop::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'          => 'Demo Retail Store',
                'address_line1' => 'Jl. Contoh No. 1',
                'address_line2' => 'Jakarta Selatan',
                'phone_number'  => '081234567890',
                'upi_id'        => 'demostore@upi',
                'footer_text'   => 'Thank you for shopping with us!',
            ]
        );

        // ── Sample products ────────────────────────────────────────────────
        $products = [
            [
                'id'      => (string) Str::uuid(),
                'name'    => 'Basmati Rice 5kg',
                'barcode' => '8991111111111',
                'price'   => 85000.00,
                'stock'   => 100,
            ],
            [
                'id'      => (string) Str::uuid(),
                'name'    => 'Cooking Oil 1L',
                'barcode' => '8992222222222',
                'price'   => 22000.00,
                'stock'   => 50,
            ],
            [
                'id'      => (string) Str::uuid(),
                'name'    => 'Mineral Water 600ml',
                'barcode' => '8993333333333',
                'price'   => 3500.00,
                'stock'   => 200,
            ],
            [
                'id'      => (string) Str::uuid(),
                'name'    => 'Instant Noodles',
                'barcode' => '8994444444444',
                'price'   => 3000.00,
                'stock'   => 150,
            ],
            [
                'id'      => (string) Str::uuid(),
                'name'    => 'Sugar 1kg',
                'barcode' => '8995555555555',
                'price'   => 16000.00,
                'stock'   => 80,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['user_id' => $user->id, 'barcode' => $productData['barcode']],
                array_merge($productData, ['user_id' => $user->id])
            );
        }

        $this->command->info('✅ Seed complete!');
        $this->command->info('   Email:    demo@example.com');
        $this->command->info('   Password: password');
    }
}
