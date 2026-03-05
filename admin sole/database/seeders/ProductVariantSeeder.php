<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'title' => 'Folding Armchair',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 579500,
            'size' => '57 x 61 x 89',
            'display_image' => 'products/armchair.png',
            'is_customizable' => true,
        ]);

        Product::create([
            'title' => 'Balcony Table',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 800000,
            'size' => '120 x 60 x 75',
            'display_image' => 'products/balcony.png',
            'is_customizable' => true,
        ]);

        Product::create([
            'title' => 'Console Table 3 Drawers',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 725000,
            'size' => '57 x 61 x 89',
            'display_image' => 'products/table3.png',
            'is_customizable' => true,
        ]);

        Product::create([
            'title' => 'Folding Chair',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 650000,
            'size' => '57 x 61 x 89',
            'display_image' => 'products/chair.png',
            'is_customizable' => true,
        ]);

        Product::create([
            'title' => 'Lounger',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 800000,
            'size' => '57 x 61 x 89',
            'display_image' => 'products/lounger.png',
            'is_customizable' => true,
        ]);

        Product::create([
            'title' => 'Chair With Brass Fitting',
            'description' => 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.',
            'price' => 600000,
            'size' => '57 x 61 x 89',
            'display_image' => 'products/chairbrass.jpg',
            'is_customizable' => true,
        ]);

        foreach (Product::all() as $product) {
            // Bahan
            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'material',
                'name' => 'Kayu Jati',
                'price' => 0, // Harga dihitung per dimensi
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'material',
                'name' => 'Kayu Jati + Rotan',
                'price' => 0, // Harga dihitung per dimensi
            ]);

            // Warna Kayu
            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'wood_color',
                'name' => 'Natural Jati',
                'price' => 60000,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'wood_color',
                'name' => 'Walnut Brown',
                'price' => 80000,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'wood_color',
                'name' => 'Coklat Salak',
                'price' => 80000,
            ]);

            // Warna Rotan
            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'rattan_color',
                'name' => 'Merah',
                'price' => 50000,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'rattan_color',
                'name' => 'Putih',
                'price' => 50000,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'rattan_color',
                'name' => 'Coklat',
                'price' => 50000,
            ]);

            ProductVariant::create([
                'product_id' => $product->id,
                'type' => 'rattan_color',
                'name' => 'Hitam',
                'price' => 50000,
            ]);
        }
    }
}
