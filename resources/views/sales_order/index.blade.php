@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="card-title mb-0 py-2">Daftar Penjualan</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-lg-flex justify-content-start">
                            <a href="{{ $create_route }}" class="btn btn-primary d-flex align-items-center">
                                <i class="mdi mdi-plus mr-1"></i>Tambah Data
                            </a>
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
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
                                            Metode Pembayaran
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
                                            Created By
                                        </th>
                                        <th>
                                            Created At
                                        </th>
                                        <th>
                                            Updated By
                                        </th>
                                        <th>
                                            Updated At
                                        </th>
                                        <th>
                                            Aksi
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
    @push('javascript-bottom')
        @include('javascript.sales_order.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
