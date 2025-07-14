<?php
    function selisih($jam_masuk, $jam_keluar)
    {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, 1, 1, 1);

        list($h, $m, $s) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, 1, 1, 1);

        $dtSelisih = $dtAkhir - $dtAwal;

        $jam = floor($dtSelisih / 3600);
        $menit = floor(($dtSelisih % 3600) / 60);
        $detik = $dtSelisih % 60;

        return sprintf("%0d:%0d:%0d", $jam, $menit, $detik);
    }
?>
@foreach ($presence as $d)
@php
    $foto_in = Storage::url('uploads/presence/'.$d->foto_in);
    $foto_out = Storage::url('uploads/presence/'.$d->foto_out);
@endphp
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $d->nik }}</td>
        <td>{{ $d->nama_lengkap }}</td>
        <td class="text-center">{{ $d->jam_in }}</td>
        <td class="text-center">
            <img src="{{ url($foto_in) }}" class="avatar" alt="">
        </td>
        <td class="text-center">{!! $d->jam_out <> null ? $d->jam_out : '<span class="badge bg-danger">Belum Absen</span>' !!}</td>
        <td class="text-center">
            @if ($d->jam_out <> null)
                <img src="{{ url($foto_out) }}" class="avatar" alt="">
            @else
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-hourglass-high">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M6.5 7h11" />
                    <path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" />
                    <path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" />
                </svg>
            @endif
        </td>
        <td class="text-center">
            @if ($d->jam_in > $jamkerja->j_masuk)
            @php
                $jamterlambat = selisih($jamkerja->j_masuk, $d->jam_in);
            @endphp
                <span class="badge bg-danger">Terlambat {{ $jamterlambat }}</span>
            @else
            <span class="badge bg-success">Tepat Waktu</span>
            @endif
        </td>
        <td class="text-center">
            <a href="#" class="btn btn-primary tampilpeta" id="{{ $d->id_presence }}">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map-search">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M11 18l-2 -1l-6 3v-13l6 -3l6 3l6 -3v7.5" />
                    <path d="M9 4v13" /><path d="M15 7v5" />
                    <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    <path d="M20.2 20.2l1.8 1.8" />
                </svg>
            </a>
        </td>
    </tr>
@endforeach
<script>
    $(function(){
        $(".tampilpeta").click(function(e){
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST'
                ,url: '/tampilpeta'
                ,data: {
                    _token:"{{ csrf_token() }}",
                    id: id
                }
                ,cache: false
                ,success: function(respond){
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-tampilpeta").modal("show");
        });
    });
</script>