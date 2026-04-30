<!DOCTYPE html>
<html lang="en">
@include('layouts.head')

<body>
    <div class="container-scroller">
        @include('layouts.navbar')
        <div class="container-fluid page-body-wrapper">
            <div class="main-panel w-100">
                <div class="content-wrapper">
                    <form action="{{ route('sales-order.store') }}" id="form_order" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-9 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h4 class="card-title mb-0 py-2">Daftar Produk</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-end">
                                            <div class="input-group col-4">
                                                <input type="text" id="search_keyword" oninput="catalogue()"
                                                    class="form-control p-3" placeholder="Search Product"
                                                    aria-describedby="search-icon-1">
                                            </div>
                                        </div>
                                        <div class="row g-4 mt-5" id="waiting-container">
                                            <div class="col-md-12">
                                                <h5 class="text-center">
                                                    <b>Harap Tunggu...</b>
                                                </h5>
                                            </div>
                                        </div>
                                        <div id="catalogue">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h4 class="card-title mb-0 py-2">Tambah Penjualan</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3">
                                            <div class="form-group">
                                                <label for="date">Tanggal Transaksi <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" value="{{ date('Y-m-d') }}"
                                                    max="{{ date('Y-m-d') }}" class="form-control" id="date"
                                                    name="date" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_method">Payment Method <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="payment_method" name="payment_method"
                                                    required>
                                                    <option hidden>-- Pilih Metode Pembayaran --</option>
                                                    @foreach ($payment_method as $pm)
                                                        @if (!is_null(old('payment_method')) && old('payment_method') == $pm->id)
                                                            <option value="{{ $pm->id }}" selected>
                                                                {{ $pm->name }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $pm->id }}">
                                                                {{ $pm->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="table-responsive mb-5">
                                            <table class="table datatable" id="product">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">
                                                            Produk
                                                        </th>
                                                        <th width="30%">
                                                            Qty
                                                        </th>
                                                        <th>
                                                            Total Harga
                                                        </th>
                                                        <th width="20%">
                                                            Aksi
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                    @if (!is_null(old('sales_order_item')))
                                                        @foreach (old('sales_order_item') as $product_id => $sales_order_item_product)
                                                            <tr id='product_{{ $product_id }}'>
                                                                <td>
                                                                    {{ $sales_order_item_product['name'] }}
                                                                    <input type="hidden"
                                                                        name="sales_order_item[{{ $product_id }}][id]"
                                                                        value="{{ $product_id }}">
                                                                    <input type="hidden"
                                                                        id="product_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][name]"
                                                                        value="{{ $sales_order_item_product['name'] }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        class="form-control text-center"
                                                                        id="qty_{{ $product_id }}" min='1'
                                                                        value="{{ $sales_order_item_product['qty'] }}"
                                                                        name="sales_order_item[{{ $product_id }}][qty]"
                                                                        oninput="validationQty(this, {{ $product_id }})">
                                                                    {{-- <input type='hidden'
                                                                        name = 'sales_order_item[{{ $product_id }}][stock]'
                                                                        value = '{{ $sales_order_item_product['stock'] }}'> --}}
                                                                    <input type="hidden"
                                                                        id="capital_price_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][capital_price]"
                                                                        value="{{ $sales_order_item_product['capital_price'] }}">
                                                                    <input type="hidden"
                                                                        id="sell_price_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][sell_price]"
                                                                        value="{{ $sales_order_item_product['sell_price'] }}">
                                                                    <input type="hidden"
                                                                        id="discount_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][discount_price]"
                                                                        value="{{ $sales_order_item_product['discount_price'] }}">
                                                                </td>
                                                                <td align="right">
                                                                    Rp. <span
                                                                        id="price_show_{{ $product_id }}">{{ \App\Helpers\NumberFormat::formatCurrency($sales_order_item_product['total_sell_price']) }}</span>
                                                                    <input type="hidden"
                                                                        id="total_sell_price_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][total_sell_price]"
                                                                        value="{{ $sales_order_item_product['total_sell_price'] }}">
                                                                    <input type="hidden"
                                                                        id="total_profit_price_{{ $product_id }}"
                                                                        name="sales_order_item[{{ $product_id }}][total_profit_price]"
                                                                        value="{{ $sales_order_item_product['total_profit_price'] }}">
                                                                </td>
                                                                <td align="center">
                                                                    <button type='button'
                                                                        class='delete-row btn btn-sm btn-danger'
                                                                        value='Delete'><i
                                                                            class='fas fa-trash'></i></button>
                                                                    <input type="hidden"
                                                                        name="sales_order_item_check[]"
                                                                        value="{{ $product_id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>
                                                            <b>Total</b>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                            <input type="hidden" name="total_capital_price"
                                                                id="total_capital_price"
                                                                value="{{ old('total_capital_price') }}">
                                                            <input type="hidden" name="total_sell_price"
                                                                id="total_sell_price"
                                                                value="{{ old('total_sell_price') }}">
                                                            <input type="hidden" name="discount_price"
                                                                id="discount_price"
                                                                value="{{ old('discount_price') }}">
                                                            <input type="hidden" name="grand_sell_price"
                                                                id="grand_sell_price"
                                                                value="{{ old('grand_sell_price') }}">
                                                            <input type="hidden" name="grand_profit_price"
                                                                id="grand_profit_price"
                                                                value="{{ old('grand_profit_price') }}">
                                                        </td>
                                                        <td align="right">
                                                            <span
                                                                id="total_price_all_product_show">{{ !is_null(old('grand_sell_price')) ? 'Rp. ' . number_format(old('grand_sell_price'), 0, ',', '.') : 'Rp. 0' }}</span>
                                                        </td>
                                                        <td>
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="float-right mt-3">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <a href="{{ route('sales-order.index') }}"
                                                    class="btn btn-secondary d-flex align-items-center mr-2">
                                                    <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                                                </a>
                                                <button type="submit"
                                                    class="btn btn-primary d-flex align-items-center"><i
                                                        class="mdi mdi-check mr-1"></i>Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    @include('layouts.script')
    @include('javascript.sales_order.script')
    <script>
        catalogue();
    </script>
</body>

</html>
