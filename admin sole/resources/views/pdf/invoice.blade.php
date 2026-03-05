<!DOCTYPE html>

<html>

<head>
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Scripts -->
    <style>
        * {
            font-family: sans-serif
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        table {
            margin-top: 24px;
            font-size: 0.75rem;
            width: 100%;
            border-radius: 0.375rem;
            overflow: hidden;
            color: #1C1111;
            text-transform: uppercase;
            background-color: #FFFFFF;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .thead {
            padding: 0.75rem 1.5rem;
            background-color: #e7e4e4;
            text-align: left;
            font-size: 0.875rem;
            color: #1C1111;
            text-transform: uppercase;
        }

        .tr:nth-child(odd) {
            background-color: #FFFFFF;
        }

        .tr:nth-child(even) {
            background-color: #D1D5DB;
        }

        .tr:hover {
            background-color: #D1D5DB;
        }

        .td {
            padding: 1rem 1.5rem;
            white-space: nowrap;
            color: #605353;
        }
    </style>
</head>

<body>
    <div class="">
        <img src="./assets/logo-mini.png" alt="">
    </div>
    <h2 style="text-align: center; margin-bottom: 4px" class="h3 text-center">Invoice Pemesanan Kadekita Coffee</h2>
    <div style="text-align: center; font-size: 14px; margin: 0; margin-bottom: 4px" class="text-sm">Jl. Ahmad Yani No. 80
        Cigugur, Kuningan.</div>

    <div style="margin-top: 24px; background: #fdfdfd; border-radius: 12px; padding: 24px"
        class="mt-4 bg-slate-50 rounded-md p-6">
        <div class="" style="width: 100%">
            <div style="margin-top: 32px">
                <table>
                    <tbody>
                        <tr style="padding: 8px 0">
                            <td>
                                <div>Nomor Transaksi </div>
                            </td>
                            <td style="text-align: end">:</td>
                            <td>KDKP1892000{{ $order->id }}</td>
                        </tr>
                        <tr style="padding: 8px 0">
                            <td>
                                <div>Tanggal pemesanan </div>
                            </td>
                            <td style="text-align: end">:</td>
                            <td>{{ $order->created_at->format('d F Y') }}</td>
                        </tr>
                        <tr style="padding: 8px 0">
                            <td>
                                <div>ID Pengguna </div>
                            </td>
                            <td style="text-align: end">:</td>
                            <td>UID09338800{{ $order->user->id }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">
            <table class="">
                <thead>
                    <tr>
                        <th scope="col" class="thead">
                            No.
                        </th>
                        <th scope="col" class="thead">
                            Nama produk
                        </th>
                        <th scope="col" class="thead">
                            Jumlah
                        </th>
                        <th scope="col" class="thead">
                            Harga
                        </th>
                        <th scope="col" class="thead">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAll = 0;
                    @endphp
                    @foreach ($orderCarts as $index => $cart)
                        @php
                            $totalAll += $cart->jumlah * $cart->product->harga;
                        @endphp
                        <tr style="border-bottom: 1px solid #dbd8d8">
                            <td class="td">{{ $index + 1 }}</td>
                            <td class="td">{{ $cart->product->nama }}</td>
                            <td class="td">{{ $cart->jumlah }}</td>
                            <td class="td">{{ number_format($cart->product->harga) }}
                            </td>
                            <td class="td" style="text-align: right">
                                {{ number_format($cart->jumlah * $cart->product->harga, 0, '.', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="td" colspan="4">Biaya pengiriman:</td>
                        <td class="td" style="text-align: right">
                            18.000
                        </td>
                    </tr>
                    <tr>
                        <td class="td" colspan="4"> <strong>Total</strong></td>
                        <td class="td" style="text-align: right">
                            <strong>{{ number_format($totalAll + 18000, 0, '.', '.') }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
