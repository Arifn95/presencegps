@extends('layouts.presence')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Peta Lokasi</div>
    <div class="right"></div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 80vh;
        width: 100%;
        border-radius: 10px;
        margin-top: 4rem;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('myscript')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const lokasiSekolahRaw = "{{ $con_lokasi->lokasi }}";
        const radius = parseInt("{{ $con_lokasi->radius }}");
        const [latSekolah, longSekolah] = lokasiSekolahRaw.split(',').map(parseFloat);

        const map = L.map('map').setView([latSekolah, longSekolah], 17);

        // Tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Marker sekolah
        L.marker([latSekolah, longSekolah])
            .addTo(map)
            .bindPopup("📍 Lokasi Sekolah");

        // Lingkaran radius sekolah
        L.circle([latSekolah, longSekolah], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.3,
            radius: radius
        }).addTo(map);

        // Lokasi pengguna
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const latUser = position.coords.latitude;
                const longUser = position.coords.longitude;

                // Gunakan icon lokal untuk marker pengguna
                const userIcon = L.icon({
                    iconUrl: "{{ asset('assets/icons/user-marker.png') }}",
                    iconSize: [48, 48],       // ukuran ikon
                    iconAnchor: [24, 48],     // posisi titik bawah tengah ikon
                    popupAnchor: [0, -40]
                });

                L.marker([latUser, longUser], { icon: userIcon })
                    .addTo(map)
                    .bindPopup("🧍 Lokasi Anda")
                    .openPopup();

                // Optional: log jarak ke console
                const distance = map.distance([latUser, longUser], [latSekolah, longSekolah]);
                console.log("Jarak ke sekolah: " + Math.round(distance) + " meter");

                // Zoom agar semua titik masuk dalam layar
                const bounds = L.latLngBounds(
                    [latUser, longUser],
                    [latSekolah, longSekolah]
                );
                map.fitBounds(bounds, { padding: [50, 50] });

            }, function (error) {
                console.warn("Geolocation gagal:", error.message);
            });
        }
    });
</script>
@endpush
