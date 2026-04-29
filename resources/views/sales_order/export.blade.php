<!DOCTYPE html>
<html>

<head>
    <title>Export Excel</title>
</head>

<body>
    <table width="100%">
        <thead>
            <tr>
                <th colspan="14" style="text-align:center;">
                    <h3>Laporan Penjualan {{ $sales_order['month'] . ' ' . $sales_order['year'] }}
                    </h3>
                </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>
                    No
                </th>
                <th>
                    No. Invoice
                </th>
                <th>
                    Tanggal
                </th>
                <th>
                    Created At
                </th>
                <th>
                    Metode Pembayaran
                </th>
                <th>
                    Produk
                </th>
                <th>
                    Harga Jual
                </th>
                <th>
                    Harga Diskon
                </th>
                <th>
                    Qty
                </th>
                <th>
                    Total Harga
                </th>
                <th>
                    Total Harga Diskon
                </th>
                <th>
                    Total Harga Akhir
                </th>
                <th>
                    Total Harga Modal
                </th>
                <th>
                    Total Keuntungan
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_sell_price = 0;
                $discount_price = 0;
                $grand_sell_price = 0;
                $total_capital_price = 0;
                $total_profit_price = 0;
            @endphp
            @foreach ($sales_order['data'] as $index => $sales_order_data)
                @php
                    $total_sell_price += intval($sales_order_data['total_sell_price']);
                    $discount_price += intval($sales_order_data['discount_price']);
                    $grand_sell_price += intval($sales_order_data['grand_sell_price']);
                    $total_capital_price += intval($sales_order_data['total_capital_price']);
                    $total_profit_price += intval($sales_order_data['grand_profit_price']);
                @endphp
                @foreach ($sales_order_data['sales_order_item'] as $sales_order_index => $sales_order_item)
                    <tr>
                        @if ($sales_order_index === 0)
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $index + 1 }}
                            </td>
                            <td style="text-align:left" rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $sales_order_data['invoice_number'] }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ date('d F Y', strtotime($sales_order_data['date'])) }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ date('d F Y', strtotime($sales_order_data['created_at'])) }}
                            </td>
                            <td rowspan="{{ count($sales_order_data['sales_order_item']) }}">
                                {{ $sales_order_data['payment_method']['name'] }}
                            </td>
                        @endif
                        <td>
                            {{ $sales_order_item['product']['name'] }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['sell_price'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['discount_price'])) }}
                        </td>
                        <td style="text-align:right">
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['qty'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(!is_null($sales_order_item['discount_price']) ? intval($sales_order_item['total_sell_price']) + intval($sales_order_item['discount_price']) * intval($sales_order_item['qty']) : intval($sales_order_item['total_sell_price'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['discount_price']) * intval($sales_order_item['qty'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['total_sell_price'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item['capital_price']) * intval($sales_order_item['qty'])) }}
                        </td>
                        <td style="text-align:right">
                            Rp.
                            {{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item['total_profit_price']) }}
                        </td>
                        {{-- @if ($product_index === 0)
                            <td style="text-align:right" rowspan="{{ count($sales_order_item['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_item['total_sell_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_item['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_item['total_capital_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_item['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_item['discount_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_item['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_item['grand_profit_price'], 0, ',', '.') }},-
                            </td>
                            <td style="text-align:right" rowspan="{{ count($sales_order_item['sales_order_item']) }}">
                                Rp. {{ number_format($sales_order_item['grand_sell_price'], 0, ',', '.') }},-
                            </td>
                        @endif --}}
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:center" colspan="9">
                    <b>Total</b>
                </td>
                <td style="text-align:right">
                    Rp. {{ App\Helpers\NumberFormat::formatCurrency($total_sell_price) }}
                </td>
                <td style="text-align:right">
                    Rp. {{ App\Helpers\NumberFormat::formatCurrency($discount_price) }}
                </td>
                <td style="text-align:right">
                    Rp. {{ App\Helpers\NumberFormat::formatCurrency($grand_sell_price) }}
                </td>
                <td style="text-align:right">
                    Rp. {{ App\Helpers\NumberFormat::formatCurrency($total_capital_price) }}
                </td>
                <td style="text-align:right">
                    Rp. {{ App\Helpers\NumberFormat::formatCurrency($total_profit_price) }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
