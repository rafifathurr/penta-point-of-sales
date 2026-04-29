@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <form class="forms-sample" method="post" action="{{ route('category-product.store') }}">
                        @csrf
                        <div class="card-header bg-white">
                            <h4 class="card-title mb-0 py-2">Tambah Kategori Produk</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Kategori Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nama Kategori Produk"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" name="description" id="description" cols="10" rows="5"
                                    placeholder="Deskripsi">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end bg-white py-3">
                            <a href="{{ route('category-product.index') }}" class="btn btn-secondary d-flex align-items-center mr-2">
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
        @include('javascript.category_product.script')
    @endpush
@endsection
