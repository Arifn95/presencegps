@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">Konfigurasi Jam Kerja</h1><br>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        {{-- Flash Message Success --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Flash Message Warning --}}
                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="/konfigurasi/updatejamkerja" method="POST">
                            @csrf

                            {{-- Awal Jam Masuk --}}
                            <h4>Awal Jam Masuk</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <input type="time" value="{{ old('awal_j_masuk', $jamkerja->awal_j_masuk) }}" id="awal_j_masuk" class="form-control" name="awal_j_masuk" placeholder="Awal Jam Masuk" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Jam Masuk --}}
                            <h4>Jam Masuk</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <input type="time" value="{{ old('j_masuk', $jamkerja->j_masuk) }}" id="j_masuk" class="form-control" name="j_masuk" placeholder="Jam Masuk" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Akhir Jam Masuk --}}
                            <h4>Akhir Jam Masuk</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <input type="time" value="{{ old('akhir_j_masuk', $jamkerja->akhir_j_masuk) }}" id="akhir_j_masuk" class="form-control" name="akhir_j_masuk" placeholder="Akhir Jam Masuk" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Jam Pulang --}}
                            <h4>Jam Pulang</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <input type="time" value="{{ old('j_pulang', $jamkerja->j_pulang) }}" id="j_pulang" class="form-control" name="j_pulang" placeholder="Jam Pulang" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-refresh">
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                        </svg>
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div> <!-- .card-body -->
                </div> <!-- .card -->
            </div> <!-- .col -->
        </div> <!-- .row -->
    </div> <!-- .container-xl -->
</div> <!-- .page-body -->

{{-- Optional: Auto-dismiss alert after 5 seconds --}}
<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            let instance = bootstrap.Alert.getOrCreateInstance(alert);
            instance.close();
        });
    }, 5000);
</script>
@endsection
