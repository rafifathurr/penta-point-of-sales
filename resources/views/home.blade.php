@extends('layouts.section')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row p-3">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        @php
                            $hour = date('H');
                            $greetings = $hour >= 18 ? 'Selamat Malam' : ($hour >= 12 ? 'Selamat Siang' : 'Selamat Pagi');
                        @endphp
                        <h2 class="font-weight-bold">
                            {{ $greetings }}, {{ Auth::user()->name }} !
                        </h2>
                        <h5 class="font-weight-normal mb-0">
                            Senang Bertemu Kembali!
                            <span class="text-primary">Selamat Menjalani Aktivitas!</span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
