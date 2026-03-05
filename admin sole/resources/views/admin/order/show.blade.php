@extends('layouts.admin')

@section('page')
    Order Detail
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Order</h2>
            <div class="flex items-center space-x-4 mt-2">
                <span class="text-lg font-medium">
                    ORD-{{ str_pad($order->tracking_number, 5, '0', STR_PAD_LEFT) }}
                </span>

                @php
                    $typeClass = $order->type === 'online'
                        ? 'bg-blue-100 text-blue-800'
                        : 'bg-purple-100 text-purple-800';
                @endphp
                <span class="px-3 py-1 text-sm rounded-full {{ $typeClass }}">
                    {{ $order->type === 'online' ? 'Online' : 'Offline' }}
                </span>
            </div>
        </div>

        <a href="{{ $order->type === 'online' ? route('order.online') : route('order.offline') }}"
           class="btn btn-secondary">
            Kembali ke Daftar Order
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KIRI --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Pelanggan --}}
            <div class="bg-white rounded shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="font-medium">{{ $order->user->name ?? $order->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No. Telepon</p>
                        <p class="font-medium">{{ $order->phone ?? ($order->customer->phone ?? '-') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-medium">{{ $order->address ?? ($order->customer->address ?? '-') }}</p>
                    </div>
                </div>
            </div>

            {{-- Item Pesanan --}}
            <div class="bg-white rounded shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Item Pesanan</h3>
                <div class="space-y-4">
                    @php
                        // Kelompokkan item berdasarkan nama produk dan kustomisasi
                        // Item dengan nama produk yang sama tetapi kustomisasi berbeda akan ditampilkan dalam card terpisah
                        $groupedItems = [];
                        foreach ($order->items as $item) {
                            $productName = $item->product ? $item->product->title : 'Produk Custom';
                            
                            // Buat key unik berdasarkan nama produk dan semua atribut kustomisasi
                            $customKey = serialize([
                                'material' => $item->material ?? '',
                                'length' => $item->length ?? '',
                                'width' => $item->width ?? '',
                                'height' => $item->height ?? '',
                                'wood_color' => $item->wood_color ?? '',
                                'rattan_color' => $item->rattan_color ?? '',
                                'price' => $item->price
                            ]);
                            
                            $groupKey = $productName . '_' . md5($customKey);
                            
                            if (!isset($groupedItems[$groupKey])) {
                                $groupedItems[$groupKey] = [
                                    'product_name' => $productName,
                                    'product_id' => $item->product_id,
                                    'items' => []
                                ];
                            }
                            
                            $groupedItems[$groupKey]['items'][] = $item;
                        }
                    @endphp

                    @foreach ($groupedItems as $group)
                        @php
                            $firstItem = $group['items'][0];
                            $totalQty = collect($group['items'])->sum('quantity');
                            $totalPrice = collect($group['items'])->sum(function($item) {
                                return $item->price * $item->quantity;
                            });
                        @endphp
                        <div class="border rounded-lg p-4">
                            <div>
                                {{-- Nama Produk: Jika ada product_id, tampilkan nama produk; jika tidak, tampilkan "Produk Custom" --}}
                                @if ($firstItem->product)
                                    <h4 class="font-medium">{{ $firstItem->product->title }}</h4>
                            @else
                                    <h4 class="font-medium">Produk Custom</h4>
                                @endif

                                {{-- Informasi Kustomisasi (jika ada material, ukuran, atau warna) --}}
                                @if ($firstItem->material || ($firstItem->length && $firstItem->width && $firstItem->height) || $firstItem->wood_color || $firstItem->rattan_color)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                        @if ($firstItem->material)
                                        <div>
                                            <p class="text-sm text-gray-500">Bahan</p>
                                                <p class="font-medium">{{ $firstItem->material }}</p>
                                        </div>
                                        @endif
                                        @if ($firstItem->length && $firstItem->width && $firstItem->height)
                                        <div>
                                            <p class="text-sm text-gray-500">Ukuran</p>
                                                <p class="font-medium">{{ number_format($firstItem->length, 2) }}cm x {{ number_format($firstItem->width, 2) }}cm x {{ number_format($firstItem->height, 2) }}cm</p>
                                        </div>
                                        @endif
                                        @if ($firstItem->wood_color || $firstItem->rattan_color)
                                        <div>
                                            <p class="text-sm text-gray-500">Warna</p>
                                                <p class="font-medium">
                                                    @if ($firstItem->wood_color)
                                                        Kayu: {{ $firstItem->wood_color }}@if ($firstItem->rattan_color)<br>@endif
                                                @endif
                                                    @if ($firstItem->rattan_color)
                                                        Rotan: {{ $firstItem->rattan_color }}
                                                @endif
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                @elseif ($firstItem->product)
                                    {{-- Informasi Produk Standar (jika ada product_id dan tidak ada kustomisasi) --}}
                                    <p class="text-sm text-gray-500 mt-1">{{ $firstItem->product->description }}</p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-sm">Size: {{ $firstItem->product->size }}</span>
                                    </div>
                                @endif

                                {{-- Qty dan Harga --}}
                                    <div class="mt-3 flex justify-between items-center">
                                    <span class="text-sm">Qty: {{ $totalQty }}</span>
                                        <div class="text-right">
                                        @if (count($group['items']) === 1)
                                            <p class="font-medium">Rp {{ number_format($firstItem->price, 0, ',', '.') }}</p>
                                        @else
                                            <div class="text-sm text-gray-500 mb-1">
                                                @foreach ($group['items'] as $item)
                                                    <div>{{ $item->quantity }}× Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                            <p class="text-sm">
                                            Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KANAN --}}
        <div class="space-y-6">
            {{-- Ringkasan --}}
            <div class="bg-white rounded shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Tanggal Pesanan:</span>
                        <span class="font-medium">{{ $order->order_date }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Pembayaran:</span>
                        <span class="font-medium">
                            @if ($order->payment_method === 'cash')
                                Tunai
                            @elseif ($order->payment_method === 'transfer')
                                Transfer
                            @else
                                Kartu Kredit
                            @endif
                        </span>
                    </div>
                    @php
                        // Hitung subtotal dari semua item (harga total barang)
                        $subtotal = $order->items->sum(function ($item) {
                            return $item->price * $item->quantity;
                        });
                        // Hitung ongkir (total_amount - subtotal)
                        $shipping = $order->total_amount - $subtotal;
                    @endphp
                    <div class="flex justify-between">
                        <span>Harga Total Barang:</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir:</span>
                        <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between font-bold">
                        <span>Total:</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Update Status</h3>

                {{-- Badge status --}}
                @php
                    $statusMap = [
                        'in_cart'     => 'bg-gray-100 text-gray-800',
                        'processing'  => 'bg-yellow-100 text-yellow-800',
                        'received'    => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-indigo-100 text-indigo-800',
                        'sending'     => 'bg-indigo-100 text-indigo-800',
                        'completed'   => 'bg-green-100 text-green-800',
                        'cancelled'   => 'bg-red-100 text-red-800',
                    ];
                    $statusClass = $statusMap[$order->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <div class="mb-6 space-y-2">
                    <p class="text-sm text-gray-500">Status Saat Ini:</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                        {{ mapStatusOrder($order->status) }}
                    </span>
                </div>

                {{-- Status pembayaran (opsional) --}}
                @if ($order->payment_status)
                    @php
                        $payMap = [
                            'waiting_payment' => 'bg-yellow-100 text-yellow-800',
                            'paid'            => 'bg-green-100 text-green-800',
                        ];
                        $payClass = $payMap[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="mb-6 space-y-2">
                        <p class="text-sm text-gray-500">Status Pembayaran:</p>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $payClass }}">
                            {{ $order->payment_status === 'waiting_payment' ? 'Menunggu Pembayaran' : 'Dibayar' }}
                        </span>
                        @if ($order->payment_time)
                            <p class="text-xs text-gray-500 mt-1">Dibayar pada: {{ $order->payment_time }}</p>
                        @endif
                    </div>
                @endif

                {{-- Form update --}}
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Ubah Status Order
                        </label>
                        <select name="status" id="status" class="form-control input-field">
                            @foreach (['received', 'processing', 'in_progress', 'sending', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                    {{ mapStatusOrder($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($order->type === 'online')
                        <div class="mb-4">
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status Pembayaran
                            </label>
                            <select name="payment_status" id="payment_status" class="form-control input-field">
                                <option value="waiting_payment" {{ $order->payment_status === 'waiting_payment' ? 'selected' : '' }}>
                                    Menunggu Pembayaran
                                </option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>
                                    Sudah Dibayar
                                </option>
                            </select>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>

            {{-- Bukti Pembayaran --}}
            @if ($order->type === 'online' && $order->payment_proof)
                <div class="bg-white rounded shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Bukti Pembayaran</h3>
                    <div class="flex justify-center">
                        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="hover:opacity-75">
                            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran"
                                 class="max-h-64 rounded border">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
