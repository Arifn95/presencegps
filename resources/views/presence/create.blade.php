@extends('layouts.presence')
@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presence</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;
    }

    #map { 
        height: 200px; 
    }

    .jam-digital-malasngoding {
        background-color: #27272783;
        position: absolute;
        top: 65px;
        right: 5px;
        z-index: 9999;
        width: 120px;
        border-radius: 10px;
        padding: 5px;
    }

    .jam-digital-malasngoding p {
        color: #fff;
        font-size: 16px;
        text-align: center;
        margin-top: 0;
        margin-bottom: 0;
    }

    .jamkerja-row {
        display: flex;
        justify-content: space-between;
        color: #fff;
        font-size: 16px;
        margin: 2px 0;
    }

    .jamkerja-label {
        width: 60px;
    }

    .jamkerja-colon {
        width: 10px;
        text-align: center;
    }

    .jamkerja-time {
        width: 50px;
        text-align: right;
    }
    

</style>

<!-- Leaflet's CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@endsection
@section('content')
    <!-- App Capsule -->
    <div class="row" style="margin-top: 60px">
        <div class="col p-0">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture"></div>
        </div>
    </div>
    <div class="jam-digital-malasngoding">
        <p>{{ date("d-m-Y") }}</p>
        <p id="jam"></p>
        <div class="jamkerja-row">
            <div class="jamkerja-label">Mulai</div>
            <div class="jamkerja-colon">:</div>
            <div class="jamkerja-time">{{ date("H:i", strtotime($jamkerja->awal_j_masuk)) }}</div>
        </div>
        <div class="jamkerja-row">
            <div class="jamkerja-label">Masuk</div>
            <div class="jamkerja-colon">:</div>
            <div class="jamkerja-time">{{ date("H:i", strtotime($jamkerja->j_masuk)) }}</div>
        </div>
        <div class="jamkerja-row">
            <div class="jamkerja-label">Akhir</div>
            <div class="jamkerja-colon">:</div>
            <div class="jamkerja-time">{{ date("H:i", strtotime($jamkerja->akhir_j_masuk)) }}</div>
        </div>
        <div class="jamkerja-row">
            <div class="jamkerja-label">Pulang</div>
            <div class="jamkerja-colon">:</div>
            <div class="jamkerja-time">{{ date("H:i", strtotime($jamkerja->j_pulang)) }}</div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @if ($cek > 0)
                <button id="takepresence" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon>    
                    Absen Pulang</button>
            @else
                <button id="takepresence" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>    
                    Absen Masuk</button>
            @endif
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
    <!-- * App Capsule -->


<audio id="notifikasi_b_masuk">
    <source src="{{ asset('assets/sound/jammasuk.mp3') }}" type="audio/mpeg">
</audio>

<audio id="notifikasi_in">
    <source src="{{ asset('assets/sound/selamatbekerja.mp3') }}" type="audio/mpeg">
</audio>

<audio id="notifikasi_a_masuk">
    <source src="{{ asset('assets/sound/akhirjammasuk.mp3') }}" type="audio/mpeg">
</audio>

<audio id="notifikasi_b_pulang">
    <source src="{{ asset('assets/sound/jampulang.mp3') }}" type="audio/mpeg">
</audio>

<audio id="notifikasi_out">
    <source src="{{ asset('assets/sound/selamatberistirahat.mp3') }}" type="audio/mpeg">
</audio>

<audio id="notifikasi_radius">
    <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
</audio>


@endsection

@push('myscript')
<script type="text/javascript">
    window.onload = function() {
        jam();
    }

    function jam() {
        var e = document.getElementById('jam')
            , d = new Date()
            , h, m, s;
        h = d.getHours();
        m = set(d.getMinutes());
        s = set(d.getSeconds());

        e.innerHTML = h + ':' + m + ':' + s;

        setTimeout('jam()', 1000);
    }

    function set(e) {
        e = e < 10 ? '0' + e : e;
        return e;
    }

</script>
<script>

    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    var notifikasi_radius = document.getElementById('notifikasi_radius');
    var notifikasi_b_masuk = document.getElementById('notifikasi_b_masuk');
    var notifikasi_a_masuk = document.getElementById('notifikasi_a_masuk');
    var notifikasi_b_pulang = document.getElementById('notifikasi_b_pulang');

    // Configure webcam
    Webcam.set({
        width: 900,
        height: 640,
        image_format: 'jpeg',
        jpeg_quality: 80
    });

    Webcam.attach('.webcam-capture');

    //configure lokasi/radius
    var lokasi = document.getElementById('lokasi');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
        lokasi.value = "Geolocation tidak didukung oleh browser ini.";
    }

    function successCallback(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        lokasi.value = `${latitude}, ${longitude}`;

        var map = L.map('map').setView([latitude, longitude], 18);
        var con_lokasi = "{{ $con_lokasi->lokasi }}";
        var lok = con_lokasi.split(",");
        var lat_lok = lok[0];
        var long_lok = lok[1];
        var radius = "{{ $con_lokasi->radius }}"
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([latitude, longitude]).addTo(map);

        var circle = L.circle([lat_lok, long_lok], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(map);
    }

    function errorCallback(error) {
        console.error("Geolocation error:", error);
        lokasi.value = "Tidak bisa mengambil lokasi.";
    }

$("#takepresence").click(function(e) {
    e.preventDefault();

    // Nyalakan kamera saat tombol diklik
    Webcam.attach('.webcam-capture');

    // Tunggu 1 detik biar kamera aktif dulu sebelum ambil gambar
    setTimeout(function () {
        Webcam.snap(function(uri) {
            let image = uri;
            let lokasiVal = $("#lokasi").val();
            // Matikan kamera setelah ambil foto
            Webcam.reset();

            $.ajax({
                type: 'POST',
                url: '/presence/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasiVal
                },
                cache: false,
                success: function(response) {
                    if (response.status == 0) {
                        let pesan = response.tipe === 'pulang' ? "Selamat Beristirahat" : "Selamat Bekerja";

                        if (response.tipe === 'pulang') {
                            notifikasi_out.play();
                        } else {
                            notifikasi_in.play();
                        }

                        Swal.fire({
                            title: 'Berhasil !',
                            text: pesan,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        });

                        setTimeout(() => location.href = '/dashboard', 1500);

                    } else if (response.status == 1) {
                        // Gagal karena radius
                        notifikasi_radius.play();
                        Swal.fire({
                            title: 'Gagal !',
                            text: "Maaf Anda Berada Diluar Radius Sekolah, Jarak anda : " + response.radius + " Meter dari Sekolah",
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                        setTimeout(() => location.href = '/dashboard', 3500);

                    } else if (response.status == 2) {
                        // Sebelum Jam Pulang
                        notifikasi_b_pulang.play();
                        Swal.fire({
                            title: 'Terlalu Cepat!',
                            text: response.message || "Belum waktunya absen Pulang.",
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        setTimeout(() => location.href = '/dashboard', 2500);
                    
                    }else if (response.status == 3) {
                        // Terlalu pagi
                        notifikasi_b_masuk.play();
                        Swal.fire({
                            title: 'Terlalu Cepat!',
                            text: response.message || "Belum waktunya absen masuk.",
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        setTimeout(() => location.href = '/dashboard', 2500);
                    }else if (response.status == 4) {
                        // absen masuk sudah berkahir
                        notifikasi_a_masuk.play();
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || "Waktu absen masuk sudah berakhir.",
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        setTimeout(() => location.href = '/dashboard', 2500);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat mengirim data');
                    console.error(xhr.responseText);
                }
            });
        });
    },1000);
});
</script>
@endpush

