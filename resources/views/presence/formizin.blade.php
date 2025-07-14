@extends('layouts.presence')
@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
    .datepicker-modal {
        max-height: 430px !important;
        border-radius: 10px !important;
    }
    .datepicker-date-display,
    .is-selected {
        background-color: #1e74fd !important;
    }
    .datepicker-done,
    .datepicker-cancel {
        color: #1e74fd !important;
    }
</style>
<!--- App Header --->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin/Sakit</div>
    <div class="right"></div>
</div>
<!--- App Header --->
@endsection
@section('content')
<div class="row" style="margin-top:70px">
    <div class="col">
        <form method="POST" action="/presence/storeizin" id="frmIzin">
            @csrf
            <div class="form-group">
                <input type="text" id="tgl_izin" name="tgl_izin" class="form-control datepicker" placeholder="tanggal">
            </div>
            <div class="form-group">
                <select name="status" id="status" class="form-control">
                    <option value="">Izin / Sakit</option>
                    <option value="i">Izin</option>
                    <option value="s">Sakit</option>
                </select>
            </div>
            <div class="form-group">
                <textarea name="ket" id="ket" cols="30" rows="5" class="form-control" placeholder="keterangan"></textarea> 
            </div>
            <div class="form-gorup">
                <button type="submit" class="btn btn-primary w-100">Kirim</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('myscript')
<script>
    var currYear = (new Date()).getFullYear();

    $(document).ready(function() {
        $(".datepicker").datepicker({
            format: "dd-mm-yyyy"    
        });

        $("#tgl_izin").change(function(e) {
            var tgl_izin = $(this).val();
            $.ajax({
                type: 'POST',
                url: '/presence/cekpengajuanizin',
                data: {
                    _token: "{{ csrf_token() }}",
                    tgl_izin: tgl_izin
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1){
                        swal.fire({
                            title: 'Oops !'
                            , text: 'Anda Telah Melakukan Input Pengajuan Izin Pada Tanggal Tersebut !'
                            , icon: 'warning'
                        }).then((result) => {
                            $("#tgl_izin").val("");
                        });
                    }
                }
            });
        });

        $("#frmIzin").submit(function() {
            var tgl_izin = $("#tgl_izin").val();
            var status = $("#status").val();
            var ket = $("#ket").val();
            if (tgl_izin == "") {
                swal.fire({
                    title: 'Oops !'
                    , text: 'Tanggal Harus Diisi'
                    , icon: 'warning'
                });
                return false;
            } else if (status == "") {
                swal.fire({
                    title: 'Oops !'
                    , text: 'Status Harus Diisi'
                    , icon: 'warning'
                });
                return false;
            } else if (ket == "") {
                swal.fire({
                    title: 'Oops !'
                    , text: 'Keterangan Harus Diisi'
                    , icon: 'warning'
                });
                return false;
            }
        })
    });
</script>
@endpush