<?php

namespace App\Http\Controllers; 

use App\Models\Product;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'size'                => 'required|string|max:255',
            'price'               => 'required|integer|min:0',
            'description'         => 'nullable|string',
            'default_length'      => 'nullable|numeric|min:0',
            'default_width'       => 'nullable|numeric|min:0',
            'default_height'      => 'nullable|numeric|min:0',
            'default_bahan'       => 'nullable|string|max:100',
            'default_color'       => 'nullable|string|max:100',
            'default_rotan_color' => 'nullable|string|max:100',
            'display_image'       => 'nullable|image|max:2048',
            
            // Validasi untuk materials
            'materials' => 'nullable|array',
            'materials.*.name' => 'required|string|max:255',
            'materials.*.price_per_10cm' => 'nullable|numeric|min:0',
            'materials.*.length_price' => 'nullable|numeric|min:0',
            'materials.*.width_price' => 'nullable|numeric|min:0',
            'materials.*.height_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('display_image')) {
            $validated['display_image'] = $request->file('display_image')
                                                 ->store('products', 'public');
        }

        // Hapus 'materials' dari $validated karena akan ditangani terpisah
        unset($validated['materials']);

        $product = Product::create($validated);

        // SIMPAN MATERIALS SEBAGAI VARIANTS
        if ($request->has('materials')) {
            foreach ($request->input('materials') as $material) {
                $product->variants()->create([
                    'type' => 'material',
                    'name' => $material['name'] ?? '',
                    'price' => 0, // Default price, bisa diisi jika diperlukan
                    'price_per_10cm' => !empty($material['price_per_10cm']) ? (float)$material['price_per_10cm'] : null,
                    'length_price' => !empty($material['length_price']) ? (float)$material['length_price'] : null,
                    'width_price' => !empty($material['width_price']) ? (float)$material['width_price'] : null,
                    'height_price' => !empty($material['height_price']) ? (float)$material['height_price'] : null,
                ]);
            }
        }

        // SIMPAN warna kayu & rotan (dengan extra_price) ke pivot color_product
        $this->syncColors($product, $request);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('woodColors', 'rattanColors');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // penting: supaya di Blade bisa akses $product->woodColors / rattanColors + pivot + variants (materials)
        // Pastikan pivot data ter-load dengan benar
        $product->load([
            'woodColors' => function ($query) {
                $query->withPivot('extra_price', 'is_default');
            },
            'rattanColors' => function ($query) {
                $query->withPivot('extra_price', 'is_default');
            },
            'variants'
        ]);

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'size'                => 'required|string|max:255',
            'price'               => 'required|integer|min:0',
            'description'         => 'nullable|string',
            'default_length'      => 'nullable|numeric|min:0',
            'default_width'       => 'nullable|numeric|min:0',
            'default_height'      => 'nullable|numeric|min:0',
            'default_bahan'       => 'nullable|string|max:100',
            'default_color'       => 'nullable|string|max:100',
            'default_rotan_color' => 'nullable|string|max:100',
            'display_image'       => 'nullable|image|max:2048',
            
            // Validasi untuk materials
            'materials' => 'nullable|array',
            'materials.*.name' => 'required|string|max:255',
            'materials.*.price_per_10cm' => 'nullable|numeric|min:0',
            'materials.*.length_price' => 'nullable|numeric|min:0',
            'materials.*.width_price' => 'nullable|numeric|min:0',
            'materials.*.height_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('display_image')) {
            if ($product->display_image) {
                Storage::disk('public')->delete($product->display_image);
            }

            $validated['display_image'] = $request->file('display_image')
                                                 ->store('products', 'public');
        }

        // Hapus 'materials' dari $validated karena akan ditangani terpisah
        unset($validated['materials']);

        $product->update($validated);

        // UPDATE MATERIALS/VARIANTS
        if ($request->has('materials')) {
            // Hapus variants material lama
            $product->variants()->where('type', 'material')->delete();
            
            // Buat variants material baru
            foreach ($request->input('materials') as $material) {
                $product->variants()->create([
                    'type' => 'material',
                    'name' => $material['name'] ?? '',
                    'price' => 0, // Default price, bisa diisi jika diperlukan
                    'price_per_10cm' => !empty($material['price_per_10cm']) ? (float)$material['price_per_10cm'] : null,
                    'length_price' => !empty($material['length_price']) ? (float)$material['length_price'] : null,
                    'width_price' => !empty($material['width_price']) ? (float)$material['width_price'] : null,
                    'height_price' => !empty($material['height_price']) ? (float)$material['height_price'] : null,
                ]);
            }
        }

        // UPDATE warna kayu & rotan di pivot (beserta extra_price-nya)
        $this->syncColors($product, $request);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->display_image) {
            Storage::disk('public')->delete($product->display_image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Simpan/update warna kayu & rotan + extra_price ke tabel pivot color_product.
     * Membaca hidden input:
     *  - new_wood_colors[][name|extra_price]
     *  - new_rattan_colors[][name|extra_price]
     */
    protected function syncColors(Product $product, Request $request): void
    {
        $defaultWoodName   = $request->input('default_color');        // dari select#default_color
        $defaultRattanName = $request->input('default_rotan_color');  // dari select#default_rotan_color

        // Bentuk data untuk sync: [color_id => ['extra_price' => ..., 'is_default' => ...], ...]
        $pivotData = [];

        // ================== WARNA KAYU ==================
        $woodRows = $request->input('new_wood_colors', []);
        if (empty($woodRows) && $product->exists) {
            $woodRows = $product->woodColors->map(function ($color) {
                return [
                    'name' => $color->name,
                    'extra_price' => $color->pivot->extra_price,
                ];
            })->toArray();
        }

        foreach ($woodRows as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }

            // Pastikan extra_price adalah angka, bukan string kosong
            $extraPrice = $row['extra_price'] ?? '';
            $extra = (!empty($extraPrice) && is_numeric($extraPrice)) ? (int)$extraPrice : 0;

            $color = Color::firstOrCreate(
                ['name' => $name, 'type' => 'wood'],
                []
            );

            $pivotData[$color->id] = [
                'extra_price' => $extra,
                'is_default'  => ($name === $defaultWoodName),
            ];
        }

        // ================== WARNA ROTAN ==================
        $rattanRows = $request->input('new_rattan_colors', []);
        if (empty($rattanRows) && $product->exists) {
            $rattanRows = $product->rattanColors->map(function ($color) {
                return [
                    'name' => $color->name,
                    'extra_price' => $color->pivot->extra_price,
                ];
            })->toArray();
        }

        foreach ($rattanRows as $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }

            // Pastikan extra_price adalah angka, bukan string kosong
            $extraPrice = $row['extra_price'] ?? '';
            $extra = (!empty($extraPrice) && is_numeric($extraPrice)) ? (int)$extraPrice : 0;

            $color = Color::firstOrCreate(
                ['name' => $name, 'type' => 'rattan'],
                []
            );

            $pivotData[$color->id] = [
                'extra_price' => $extra,
                'is_default'  => ($name === $defaultRattanName),
            ];
        }

        // Hapus relasi lama dan ganti dengan yang baru
        $product->colors()->sync($pivotData);
    }
}
