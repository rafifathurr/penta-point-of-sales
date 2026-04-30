@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="card-title mb-0 py-2">Detail Pejualan #{{ $sales_order->id }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">No Invoice</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->invoice_number }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tanggal Penjualan</label>
                            <div class="col-sm-9 col-form-label">
                                {{ !is_null($sales_order->date) ? date('d F Y', strtotime($sales_order->date)) : date('d F Y', strtotime($sales_order->created_at)) }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Metode Pembayaran</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->payment_type == 0 ? (!is_null($sales_order->payment_method_id) ? $sales_order->paymentMethod->name : 'Shopee') : 'Point' }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Created By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Created At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($sales_order->created_at)) }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $sales_order->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 col-form-label font-weight-bold">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($sales_order->updated_at)) }}
                            </div>
                        </div>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered datatable" id="product_size">
                                <thead>
                                    <tr>
                                        <th width="15%">
                                            Product
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
                                        @if ($show_capital_price)
                                            <th>
                                                Total Harga Modal
                                            </th>
                                            <th>
                                                Total Keuntungan
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    @foreach ($sales_order->salesOrderItem as $sales_order_item)
                                        <tr>
                                            <td>
                                                {{ $sales_order_item->product->name }}
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item->sell_price) }}
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency(!is_null($sales_order_item->discount_price) ? intval($sales_order_item->discount_price) : 0) }}
                                            </td>
                                            <td>
                                                {{ $sales_order_item->qty }} Pcs
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency(!is_null($sales_order_item->discount_price) ? intval($sales_order_item->total_sell_price) + intval($sales_order_item->discount_price) * intval($sales_order_item->qty) : intval($sales_order_item->total_sell_price)) }}
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency(!is_null($sales_order_item->discount_price) ? intval($sales_order_item->discount_price) * intval($sales_order_item->qty) : 0) }}
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item->total_sell_price) }}
                                            </td>
                                            @if ($show_capital_price)
                                                <td align="right">
                                                    Rp.
                                                    {{ \App\Helpers\NumberFormat::formatCurrency(intval($sales_order_item->capital_price) * intval($sales_order_item->qty)) }}
                                                </td>
                                                <td align="right">
                                                    Rp.
                                                    {{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item->total_profit_price) }}
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" align="right">
                                            <b> Total</b>
                                        </td>
                                        <td align="right">
                                            Rp.
                                            {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->total_sell_price) }}
                                        </td>
                                        <td align="right">
                                            Rp.
                                            {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->discount_price) }}
                                        </td>
                                        <td align="right">
                                            Rp.
                                            {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->grand_sell_price) }}
                                        </td>
                                        @if ($show_capital_price)
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->total_capital_price) }}
                                            </td>
                                            <td align="right">
                                                Rp.
                                                {{ \App\Helpers\NumberFormat::formatCurrency($sales_order->grand_profit_price) }}
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-end bg-white py-3">
                        <a href="{{ route('sales-order.index') }}"
                            class="btn btn-secondary d-flex align-items-center mr-2">
                            <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                        </a>
                        <a href="{{ route('sales-order.invoice', ['id' => $sales_order->id]) }}"
                            class="btn btn-light d-flex align-items-center mr-2" target="_blank">
                            <i class="mdi mdi-printer mr-1"></i>
                            Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.sales_order.script')
    @endpush
@endsection
