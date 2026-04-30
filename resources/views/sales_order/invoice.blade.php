<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembelanjaan - {{ $sales_order->invoice_number }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
        }

        .center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
        }

        .right {
            text-align: right;
        }

        table td {
            padding-top: 5px;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>

    <div class="center">
        <img src="{{ asset('images/PentaPos.png') }}" alt="logo" width="30%" /><br>
    </div>
    <br>
    <div class="line"></div>
    <br>
    <table>
        <tr>
            <td>No: {{ $sales_order->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tanggal: {{ date('d/m/Y', strtotime($sales_order->date)) }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $sales_order->createdBy->name ?? 'Admin' }}</td>
        </tr>
        <tr>
            <td>Dibuat Pada: {{ date('d/m/Y H:i:s', strtotime($sales_order->created_at)) }}</td>
        </tr>
    </table>
    <br>
    <div class="line"></div>
    <br>
    <table>
        @foreach ($sales_order->salesOrderItem as $sales_order_item)
            <tr>
                <td colspan="2">{{ $sales_order_item->product->name }}</td>
            </tr>
            <tr>
                <td>{{ $sales_order_item->qty }} x
                    Rp. {{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item->sell_price) }}</td>
                <td class="right">
                    Rp. {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item->sell_price) * intval($sales_order_item->qty)) }}
                </td>
            </tr>
            @if (intval($sales_order_item->discount_price) != 0)
                <tr>
                    <td>Diskon</td>
                    <td class="right">
                        -
                        Rp. {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item->discount_price) * intval($sales_order_item->qty)) }}
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    <br>
    <div class="line"></div>
    <br>
    <table>
        <tr>
            <td>Total</td>
            <td class="right">Rp.
                {{ \App\Helpers\NumberFormat::formatCurrency(!is_null($sales_order->discount_price) ? intval($sales_order->grand_sell_price) + intval($sales_order->discount_price) : $sales_order->grand_sell_price) }}
            </td>
        </tr>
        <tr>
            <td>Total Diskon</td>
            <td class="right">Rp.
                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->discount_price ?? 0) }}</td>
        </tr>
        <tr>
            <td>Total Akhir</td>
            <td class="right">Rp.
                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->grand_sell_price) }}
            </td>
        </tr>
    </table>
    <br>
    <div class="line"></div>
    <br>
    <div class="center">
        --- TERIMA KASIH TELAH BELANJA DITOKO KAMI ---<br>
        Barang Yang Sudah Dibayar<br>
        Tidak Dapat Dikembalikan
    </div>

</body>

</html>
