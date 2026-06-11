<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        $productsData = [
            [
                'barcode' => '899100110111',
                'name' => 'Indomie Goreng',
                'category' => 'Makanan Ringan',
                'price' => 3500,
                'purchase_price' => 2800,
                'unit' => 'pcs',
                'min_stock' => 20
            ],
            [
                'barcode' => '899100210122',
                'name' => 'Teh Botol Sosro',
                'category' => 'Minuman',
                'price' => 5000,
                'purchase_price' => 4000,
                'unit' => 'botol',
                'min_stock' => 15
            ],
            [
                'barcode' => '899100310133',
                'name' => 'Sampoerna Mild',
                'category' => 'Rokok',
                'price' => 25000,
                'purchase_price' => 22000,
                'unit' => 'pak',
                'min_stock' => 10
            ],
            [
                'barcode' => '899100410144',
                'name' => 'Lifebuoy Sabun Mandi',
                'category' => 'Perlengkapan Mandi',
                'price' => 4500,
                'purchase_price' => 3800,
                'unit' => 'pcs',
                'min_stock' => 10
            ],
            [
                'barcode' => '899100510155',
                'name' => 'Kornet Pronas',
                'category' => 'Makanan Kaleng',
                'price' => 25000,
                'purchase_price' => 21000,
                'unit' => 'kaleng',
                'min_stock' => 8
            ],
            [
                'barcode' => '899100610166',
                'name' => 'Ultra Milk',
                'category' => 'Minuman',
                'price' => 7000,
                'purchase_price' => 6000,
                'unit' => 'kotak',
                'min_stock' => 12
            ],
            [
                'barcode' => '899100710177',
                'name' => 'Oreo',
                'category' => 'Makanan Ringan',
                'price' => 8000,
                'purchase_price' => 6800,
                'unit' => 'bungkus',
                'min_stock' => 15
            ],
            [
                'barcode' => '899100810188',
                'name' => 'Pepsodent',
                'category' => 'Perlengkapan Mandi',
                'price' => 12000,
                'purchase_price' => 10000,
                'unit' => 'pcs',
                'min_stock' => 8
            ],
        ];

        foreach ($branches as $branch) {
            foreach ($productsData as $product) {
                Product::create([
                    'barcode' => $product['barcode'] . $branch->id,
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'purchase_price' => $product['purchase_price'],
                    'stock' => rand(30, 150),
                    'min_stock' => $product['min_stock'],
                    'unit' => $product['unit'],
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
