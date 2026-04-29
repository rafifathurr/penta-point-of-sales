@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <form class="forms-sample" method="post" action="{{ route('user.update', ['id' => $user->id]) }}">
                        @csrf
                        @method('patch')
                        <div class="card-header bg-white">
                            <h4 class="card-title mb-0 py-2">Ubah User #{{ $user->id }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ $user->username }}" placeholder="Username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group">
                                <label for="roles">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="roles" name="roles" required {{ $role_disabled }}>
                                    <option hidden>Choose Role</option>
                                    @foreach ($roles as $role)
                                        @if ($user->getRoleNames()[0] == $role->name)
                                            <option value="{{ $role->name }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="re_password">Re Password</label>
                                <input type="password" class="form-control" id="re_password" name="re_password"
                                    placeholder="Re Password">
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-end bg-white py-3">
                            <a href="{{ route('user.index') }}" class="btn btn-secondary d-flex align-items-center mr-2">
                                <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center"><i class="mdi mdi-check mr-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.user.script')
    @endpush
@endsection
