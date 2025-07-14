<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; //1 atau januari
        $tahunini = date("Y"); // 2025

        $hariinggris = date("D", strtotime($hariini));
        $hariindonesia = [
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu',
        ];
        $namahari = $hariindonesia[$hariinggris] ?? 'Tidak diketahui';

         // jam kerja
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();
        $jam_masuk = $jamkerja->j_masuk; // contoh: "06:30:00"

        $nik = Auth::guard("pengajar")->user()->nik;
        $presencehariini = DB::table('presence')->where('nik', $nik)->where('tgl_presence', $hariini)->first();
        $historibulanini = DB::table('presence')
            ->join('jamkerja', 'presence.id_jamkerja', '=', 'jamkerja.id_jamkerja')
            ->where('presence.nik', $nik)
            ->whereRaw('MONTH(presence.tgl_presence) = ?', [$bulanini])
            ->whereRaw('YEAR(presence.tgl_presence) = ?', [$tahunini])
            ->orderBy('presence.tgl_presence')
            ->select('presence.*', 'jamkerja.j_masuk', 'jamkerja.j_pulang')
            ->get();
        
        foreach ($historibulanini as $d) {
        $hariEng = date("D", strtotime($d->tgl_presence));
        $d->namahari = $hariindonesia[$hariEng] ?? 'Tidak diketahui';
        }
        
        $rekappresence = DB::table('presence')
            ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > ?, 1, 0)) as jmltelat', [$jam_masuk])
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presence)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presence)="'. $tahunini .'"')
            ->first();
        
        $daftarhadirhariini = DB::table('presence')
            ->join('pengajar','presence.nik','=','pengajar.nik')
            ->where('tgl_presence', $hariini)
            ->orderBy('jam_in')
            ->get();
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_izin)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_izin)="'. $tahunini .'"')
            ->where('stat_aprvd', 1)
            ->first();
        
        return view('dashboard.dashboard', compact('presencehariini', 'historibulanini','namabulan','bulanini','tahunini','rekappresence','daftarhadirhariini','rekapizin','namahari'));
    }

    //dashboard admin
    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        // jam kerja
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();
        $jam_masuk = $jamkerja->j_masuk; // contoh: "06:30:00"

        $rekappresence = DB::table('presence')
            ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > ?, 1, 0)) as jmltelat', [$jam_masuk])
            ->where('tgl_presence', $hariini)
            ->first();
            
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('stat_aprvd', 1)
            ->first();
            
        return view('dashboard.dashboardadmin',compact('rekappresence', 'rekapizin'));
    }
}