@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="card-title mb-0 py-2">Detail Produk #{{ $product->id }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nama Produk</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Kode Barcode Produk</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->barcode }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Kategori Produk</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->categoryProduct->name ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Status</label>
                            <div class="col-sm-9 col-form-label">
                                @if ($product->status == 1)
                                    <span class="badge badge-success pl-3 pr-3">Active</span>
                                @elseif($product->status == 0)
                                    <span class="badge badge-danger pl-3 pr-3">Inactive</span>
                                @endif
                            </div>
                        </div>
                        @if ($can_show_capital)
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold">Harga Modal</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $product->capital_price ? 'Ro. ' . \App\Helpers\NumberFormat::formatCurrency($product->capital_price) : '-' }}
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Harga Jual</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->sell_price ? 'Ro. ' . \App\Helpers\NumberFormat::formatCurrency($product->sell_price) : '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Harga Diskon</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->discount_price ? 'Ro.' . \App\Helpers\NumberFormat::formatCurrency($product->discount_price) : '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Foto Produk</label>
                            <div class="col-sm-9 col-form-label">
                                <img width="10%" src="{{ asset($product->picture) }}" alt=""
                                    class="border border-1-default">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Deskripsi</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->description ?? '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Created By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->createdBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Created At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($product->updated_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $product->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($product->updated_at)) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-end bg-white py-3">
                        <a href="{{ route('product.index') }}" class="btn btn-secondary d-flex align-items-center mr-2">
                            <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.product.script')
    @endpush
@endsection
