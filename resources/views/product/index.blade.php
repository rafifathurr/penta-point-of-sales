@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="card-title mb-0 py-2">Daftar Produk</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start">
                            @if ($can_create)
                                <a href="{{ route('product.create') }}" class="btn btn-primary d-flex align-items-center">
                                    <i class="mdi mdi-plus mr-1"></i>Tambah Data
                                </a>
                            @endif
                        </div>
                        <div class="table-responsive pt-3">
                            <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                            <table class="table table-bordered datatable" id="dt-product">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Foto Produk
                                        </th>
                                        <th>
                                            Nama
                                        </th>
                                        <th>
                                            Kategori Produk
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>
                                            Harga Jual
                                        </th>
                                        <th>
                                            Harga Diskon
                                        </th>
                                        <th>
                                            Created At
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
        @include('javascript.product.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
