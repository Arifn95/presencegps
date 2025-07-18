@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title">Data Guru</h1><br>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if (Session::get('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                @endif
                                @if (Session::get('warning'))
                                    <div class="alert alert-warning">
                                        {{ Session::get('warning') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahpengajar">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <form method="GET" action="{{ route('pengajar.index') }}">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama..." value="{{ request('search') }}"autocomplete="off">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                            <a href="{{ route('pengajar.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>No. HP</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajar as $d )
                                @php
                                    $path = Storage::url('uploads/pengajar/'.$d->foto);
                                @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $d->nik }}</td>
                                        <td>{{ $d->nama_lengkap }}</td>
                                        <td>{{ $d->jabatan }}</td>
                                        <td class="text-center">{{ $d->no_hp }}</td>
                                        <td class="text-center">
                                            @php
                                                $defaultFoto = asset('assets/img/nofoto.png');
                                                $fotoPath = storage_path('app/public/uploads/pengajar/' . $d->foto);
                                                $fotoUrl = asset('storage/uploads/pengajar/' . $d->foto);
                                            @endphp

                                            @if (!empty($d->foto) && file_exists($fotoPath))
                                                <img src="{{ $fotoUrl }}" class="avatar" alt="">
                                            @else
                                                <img src="{{ $defaultFoto }}" class="avatar" alt="">
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <!-- button edit -->
                                            <a href="#" class="btn btn-sm btn-primary btn-edit d-inline-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; padding: 6px;"
                                                data-nik="{{ $d->nik }}"
                                                data-nama="{{ $d->nama_lengkap }}"
                                                data-jabatan="{{ $d->jabatan }}"
                                                data-nohp="{{ $d->no_hp }}"
                                                data-foto="{{ $d->foto }}">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>
                                            <!-- button delete -->
                                            <a href="#" class="btn btn-sm btn-danger btn-delete d-inline-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; padding: 6px;" data-nik="{{ $d->nik }}">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $pengajar->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-inputpengajar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/pengajar/store" method="POST" id="frmPengajar" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                                    <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                    <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                                    <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                                    <path d="M5 11h1v2h-1z" />
                                    <path d="M10 11l0 2" />
                                    <path d="M14 11h1v2h-1z" />
                                    <path d="M19 11l0 2" />
                                </svg>
                                </span>
                                <input type="text" value="" id="nik" class="form-control" name="nik" placeholder="NIK" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                                </span>
                                <input type="text" value="" id="nama_lengkap" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building-cog">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 21h9" />
                                    <path d="M9 8h1" />
                                    <path d="M9 12h1" />
                                    <path d="M9 16h1" />
                                    <path d="M14 8h1" />
                                    <path d="M14 12h1" />
                                    <path d="M5 21v-16c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h10c.53 0 1.039 .211 1.414 .586c.375 .375 .586 .884 .586 1.414v7" />
                                    <path d="M16 18c0 .53 .211 1.039 .586 1.414c.375 .375 .884 .586 1.414 .586c.53 0 1.039 -.211 1.414 -.586c.375 -.375 .586 -.884 .586 -1.414c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414z" />
                                    <path d="M18 14.5v1.5" />
                                    <path d="M18 20v1.5" />
                                    <path d="M21.032 16.25l-1.299 .75" />
                                    <path d="M16.27 19l-1.3 .75" />
                                    <path d="M14.97 16.25l1.3 .75" />
                                    <path d="M19.733 19l1.3 .75" />
                                </svg>
                                </span>
                                <input type="text" value="" id="jabatan" class="form-control" name="jabatan" placeholder="Jabatan" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                </svg>
                                </span>
                                <input type="text" value="" id="no_hp" class="form-control" name="no_hp" placeholder="Nomor HP" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <input type="file" name="foto" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="form_group">
                                <button class="btn btn-primary w-100">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 14l11 -11" />
                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                    </svg>
                                    Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-editpengajar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="frmEditPengajar" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_nik_lama" name="nik_lama">
                    <div class="mb-3">
                        <input type="text" id="edit_nik" name="nik" class="form-control" placeholder="NIK">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="edit_jabatan" name="jabatan" class="form-control" placeholder="Jabatan">
                    </div>
                    <div class="mb-3">
                        <input type="text" id="edit_no_hp" name="no_hp" class="form-control" placeholder="Nomor HP">
                    </div>
                    <div class="mb-3">
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
        $("#btnTambahpengajar").click(function() {
            $("#modal-inputpengajar").modal("show");
        });

        $("#frmPengajar").submit(function(e){
            e.preventDefault();

            var nik = $("#nik").val();
            var nama_lengkap = $("#nama_lengkap").val();
            var jabatan = $("#jabatan").val();
            var no_hp = $("#no_hp").val();
            if (nik == "" || nama_lengkap == "" || jabatan == "" || no_hp == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Semua field harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (nik == "") {
                        $("#nik").focus();
                    } else if (nama_lengkap == "") {
                        $("#nama_lengkap").focus();
                    } else if (jabatan == "") {
                        $("#jabatan").focus();
                    } else if (no_hp == "") {
                        $("#no_hp").focus();
                    }
                });
                return false;
            }
            this.submit();
        });

        $(".btn-edit").click(function() {
            const nik = $(this).data('nik');
            const nama = $(this).data('nama');
            const jabatan = $(this).data('jabatan');
            const nohp = $(this).data('nohp');

            $("#edit_nik").val(nik);
            $("#edit_nik_lama").val(nik); // Simpan nik lama
            $("#edit_nama_lengkap").val(nama);
            $("#edit_jabatan").val(jabatan);
            $("#edit_no_hp").val(nohp);

            $("#frmEditPengajar").attr("action", "/pengajar/update/" + nik); // masih pakai nik lama
            $("#modal-editpengajar").modal("show");
        });

        $(".btn-delete").click(function(e) {
            e.preventDefault();
            let nik = $(this).data("nik");

            Swal.fire({
                title: 'Yakin mau hapus?',
                text: "Data dengan NIK " + nik + " akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke route delete
                    window.location.href = "/pengajar/delete/" + nik;
                }
            });
        });

    });
</script>
@endpush