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
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, zsans-serif;
            font-size: 10px;
        }

        #title {
            font-size: 16px;
            font-weight: bold;
        }

        .tabeldata {
            margin-top: 40px;
        }

        .tabeldata td {
            padding: 5px;
        }

        .tabelpresence {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            
            word-break: break-word;
        }

        .tabelpresence th,
        .tabelpresence td {
            border: 1px solid #000000;
            padding: 2px 1px;
            font-size: 10px;
            text-align: center;
        }

        .tabelpresence th {
            background-color: #e1e1e1;
        }

        .tabelpresence td span {
            display: block;
            line-height: 1.1;
        }

        .foto {
            width: 50px;
            height: 40px;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
    
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 landscape">

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
                        REKAPITULASI ABSENSI GURU<br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        MI.NURUL HAKIM JAKARTA<br>
                    </span>
                    <span><i>Jl. Palem Raya No.71 2, RT.2/RW.8, Petukangan Utara, Kec. Pesanggrahan, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12260. No.Tlp : 089627439838</i></span>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #000; margin-top: 10px; margin-bottom: 10px;"></div>
        <table class="tabelpresence">
            <tr>
                <th rowspan="2">NIK</th>
                <th rowspan="2">Nama</th>
                <th colspan="31">Tanggal</th>
                <th rowspan="2">TH</th>
                <th rowspan="2">TT</th>
            </tr>
            <tr>
                <?php
                for($i=1; $i<=31; $i++){
                ?>
                <th>{{ $i }}</th>
                <?php
                }
                ?>

            </tr>
            @foreach ($rekap as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_lengkap }}</td>

                    <?php
                    $totalhadir = 0;
                    $totalterlambat = 0;
                    for($i=1; $i<=31; $i++){
                        $tgl = "tgl_".$i;
                        if(empty($d->$tgl)){
                            $hadir = ['',''];
                            $totalhadir +=0;
                        }else {
                            $hadir = explode("-",$d->$tgl);
                            $totalhadir += 1;
                            if ($hadir[0] > $jamkerja->j_masuk) {
                                $totalterlambat +=1;
                            }
                        }
                    ?>

                    <td>
                        <span style="color:{{ $hadir[0] > $jamkerja->j_masuk ? 'red' : '' }}">{{ $hadir[0] }}</span>
                        <span style="color:{{ $hadir[1] < $jamkerja->j_pulang ? 'red' : '' }}">{{ $hadir[1] }}</span>
                    </td>
                    
                    <?php
                    }
                    ?>
                    <td>{{ $totalhadir }}</td>
                    <td>{{ $totalterlambat }}</td>
                </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top: 100px">
            <tr>
                <td></td>
                <td style="text-align: left">Jakarta, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: left; vertical-align:bottom; padding-right: 450px" height="120px">
                    <u>Rochwandi</u><br>
                    <i><b>Operator</b></i>
                </td>
                
                <td style="text-align: left ; vertical-align: bottom;">
                    <u>Nur Fadlah</u><br>
                    <i><b>Kepala Madrasah</b></i>
                </td>
            </tr>
        </table>
    </section>

</body>

</html>