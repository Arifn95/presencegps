<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>A4</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page { 
            size: A4;
        }
        
        #title{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldata{
            margin-top: 40px;
        }

        .tabeldata td {
            padding: 5px;
        }

        .tabelpresence {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresence  tr th {
            border: 1px solid #000000;
            padding: 8px;
            background-color: #e1e1e1;
        }

        .tabelpresence  tr td {
            border: 1px solid #000000;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }

        .foto{
            width: 50px;
            height: 40px;
        }
    </style>
    
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

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
    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">

    <!-- Write HTML just like a web page -->
        <table style="width: 100%">
            <tr>
                <td style="width: 100px">
                    <img src="{{ asset('assets/img/logopresence.png') }}" width="90" height="90" alt="">
                </td>
                <td>
                    <span id="title">
                        LAPORAN ABSENSI GURU<br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        MI.NURUL HAKIM JAKARTA<br>
                    </span>
                    <span><i>Jl. Palem Raya No.71 2, RT.2/RW.8, Petukangan Utara, Kec. Pesanggrahan, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12260. No.Tlp : 089627439838</i></span>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #000; margin-top: 10px; margin-bottom: 10px;"></div>
        <table class="tabeldata">
            <tr>
                <td rowspan="5">
                    @php
                        $foto = $pengajar->foto ?? null;
                        $fotoPath = storage_path('app/public/uploads/pengajar/' . $foto);
                        $fotoUrl = asset('storage/uploads/pengajar/' . $foto);
                    @endphp

                    @if ($foto && file_exists($fotoPath))
                        <img src="{{ $fotoUrl }}" alt="" width="130px" height="150px">
                    @else
                        <img src="{{ asset('assets/img/nofoto.png') }}" alt="" width="130px" height="150px">
                    @endif
                </td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $pengajar->nik }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $pengajar->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $pengajar->jabatan }}</td>
            </tr>
            <tr>
                <td>No. HP</td>
                <td>:</td>
                <td>{{ $pengajar->no_hp }}</td>
            </tr>
        </table>
        <table class="tabelpresence">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>foto</th>
                <th>Keterangan</th>
                <th>Total Jam</th>
            </tr>
            @foreach ($presence as $d)
            @php
                $jamterlambat = selisih($jamkerja->j_masuk, $d->jam_in);
            @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date("d-m-Y",strtotime($d->tgl_presence)) }}</td>
                    <td>{{ $d->jam_in }}</td>
                    <td>
                        @if ($d->foto_in)
                            Absen
                        @else
                            Belum Absen
                        @endif
                    </td>
                    <td>{{ $d->jam_out <> null ? $d->jam_out : 'Belum Absen' }}</td>
                    <td>
                        @if ($d->foto_out)
                            Absen
                        @else
                            Belum Absen
                        @endif
                    </td>
                    <td>
                        @if ($d->jam_in > $jamkerja->j_masuk)
                            Terlambat {{ $jamterlambat }}
                        @else
                            Tepat Waktu
                        @endif
                    </td>
                    <td>
                        @if ($d->jam_out <> null)
                        @php
                            $jmljamkerja = selisih($d->jam_in, $d->jam_out);
                        @endphp
                        @else
                        @php
                            $jmljamkerja = 0;
                        @endphp
                        @endif
                        {{ $jmljamkerja }}
                    </td>
                </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top: 100px">
            <tr>
                <td colspan="2" style="text-align: right">Jakarta, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: left; vertical-align:bottom; padding-right: 450px" height="120px">
                    <u>Rochwandi</u><br>
                    <i><b>Operator</b></i>
                </td>
                <td style="text-align: left; vertical-align: bottom;">
                    <u>Nur Fadlah</u><br>
                    <i><b>Kepala Madrasah</b></i>
                </td>
            </tr>
        </table>
    </section>

</body>

</html>