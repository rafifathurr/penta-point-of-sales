@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin transparent">
                <div class="card px-3">
                    <div class="card-body">
                        <h4 class="py-3"><b>Ringkasan Statistik Produk</b></h4>
                        <div class="row">
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-dark-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Produk</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_product">-</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Active Produk</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_active_product">-</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Inactive Product</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_inactive_product">-</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 stretch-card transparent">
                                <div class="card card-light-blue">
                                    <div class="card-body">
                                        <h5 class="mb-4 text-bold"><b>Total Kategori Produk</b></h5>
                                        <span class="text-right">
                                            <h5 class="mb-2" id="total_category_product">-</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card px-3 mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between my-3">
                            <div class="p-0">
                                <h4 class="py-3"><b>Statistik Penjualan</b></h4>
                            </div>
                            <div class="p-0">
                                <div class="row ml-auto">
                                    <div class="col-md-4 pb-3 pl-0">
                                        <select class="form-control" id="sales_order_month">
                                            @foreach ($dashboard['months'] as $month_num => $month)
                                                <option value="{{ $month_num }}"
                                                    @if ($month_num == date('m')) selected @endif>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 pb-3 pl-0">
                                        <select class="form-control" id="sales_order_year">
                                            @foreach ($dashboard['years'] as $year)
                                                <option value="{{ $year['year'] }}"
                                                    @if ($year['year'] == date('Y')) selected @endif>{{ $year['year'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 my-auto pb-3 pl-0">
                                        <button class="btn btn-primary d-flex align-items-center" onclick="dashboardSalesOrder()" title="Filter">
                                            <i class="mdi mdi-filter mr-1"></i>
                                            Saring
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row py-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 mb-3 stretch-card transparent">
                                        <div class="card card-tale">
                                            <div class="card-body">
                                                <h5 class="mb-4 text-bold"><b>Total Pemasukan</b></h5>
                                                <span class="text-right">
                                                    <h5 class="mb-2" id="total_income">-</h5>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3 stretch-card transparent">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h5 class="mb-4 text-bold"><b>Total Keuntungan</b></h5>
                                                <span class="text-right">
                                                    <h5 class="mb-2" id="total_profit">-</h5>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3 stretch-card transparent">
                                        <div class="card card-light-blue">
                                            <div class="card-body">
                                                <h5 class="mb-4 text-bold"><b>Total Penjualan</b></h5>
                                                <span class="text-right">
                                                    <h5 class="mb-2" id="total_sales_order">-</h5>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3 stretch-card transparent">
                                        <div class="card card-light-danger">
                                            <div class="card-body">
                                                <h5 class="mb-4 text-bold"><b>Total Produk Terjual</b></h5>
                                                <span class="text-right">
                                                    <h5 class="mb-2" id="total_product_sold">-</h5>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mt-4 mb-4"><b>Grafik Penjualan</b></h4>
                                <canvas id="sales-chart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-12 p-3 mt-3 border-top border-top-1">
                            <div class="d-flex justify-content-between my-3">
                                <div class="p-0 my-auto">
                                    <h5><b>Laporan Penjualan</b></h5>
                                </div>
                                <div class="p-0">
                                    <div class="input-group w-100 mx-auto d-flex">
                                        <button class="btn btn-success d-flex align-items-center" onclick="exportSalesOrder()" title="Export Laporan">
                                            <i class="mdi mdi-file-excel mr-1"></i>
                                            Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered datatable" id="dt-sales-order">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                No. Invoice
                                            </th>
                                            <th>
                                                Tanggal
                                            </th>
                                            <th>
                                                Total Harga
                                            </th>
                                            <th>
                                                Total Diskon
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.dashboard.script')
        <script>
            dashboardSalesOrder();
            // dashboardCoa();
            dashboardProduct();
            // dashboardStock();
        </script>
    @endpush
@endsection
