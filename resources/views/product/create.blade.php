@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <form class="forms-sample" method="post" action="{{ route('product.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-header bg-white">
                            <h4 class="card-title mb-0 py-2">Tambah Produk</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">Nama Produk <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Nama Produk" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="barcode">Kode Barcode <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="barcode" name="barcode"
                                            placeholder="Kode Barcode Produk" value="{{ old('barcode') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_product">Kategori Produk <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="category_product" name="category_product" required>
                                            <option disabled hidden selected>-- Pilih Kategori Produk --</option>
                                            @foreach ($category_product as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ !is_null(old('category_product')) && old('category_product') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status Produk <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="" hidden>-- Pilih Status Produk --</option>
                                            <option value="0"
                                                {{ !is_null(old('status')) && old('status') == 0 ? 'selected' : '' }}>
                                                Inactive</option>
                                            <option value="1"
                                                {{ !is_null(old('status')) && old('status') == 1 ? 'selected' : '' }}>Active
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="capital_price">Harga Modal <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="capital_price" name="capital_price"
                                            placeholder="Harga Modal" min="0" value="{{ old('capital_price') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sell_price">Harga Jual <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="sell_price" name="sell_price"
                                            placeholder="Harga Jual" min="0" value="{{ old('sell_price') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="discount_price">Harga Diskon</label>
                                        <input type="number" class="form-control" id="discount_price" name="discount_price"
                                            placeholder="Harga Diskon (Opsional)" min="0" value="{{ old('discount_price') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="picture">Foto Produk <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="picture" name="picture"
                                    placeholder="Attach Picture" value="{{ old('picture') }}"
                                    accept="image/jpeg,image/jpg,image/png" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="5"
                                    placeholder="Deskripsi">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end bg-white py-3">
                            <a href="{{ route('product.index') }}"
                                class="btn btn-secondary d-flex align-items-center mr-2">
                                <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center"><i
                                    class="mdi mdi-check mr-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.product.script')
    @endpush
@endsection
