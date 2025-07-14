<style>
    #map { 
        height: 250px; 
    }
</style>
{{ $presence->location_in }}
<div id="map"></div>
<script>
    var lokasi = "{{ $presence->location_in }}";
    var lok = lokasi.split(",");
    var latitude = lok[0];
    var longitude = lok[1];

    var map = L.map('map').setView([latitude, longitude], 18);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([latitude, longitude]).addTo(map);
    var circle = L.circle([-6.226235, 106.758007], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 23
    }).addTo(map);

    var popup = L.popup({
    offset: [0, -20]
    })
    .setLatLng([latitude, longitude])
    .setContent("{{ $presence->nama_lengkap }}")
    .openOn(map);

</script>