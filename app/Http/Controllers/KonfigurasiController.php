<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class KonfigurasiController extends Controller
{
    public function lokasi()
    {
        $lokasi = DB::table('config_lokasi')->where('id_config_lokasi', 1)->first();
        return view('konfigurasi.lokasi', compact('lokasi'));
    }

    public function updatelokasi(Request $request)
    {
        $request->validate([
            'lokasi' => 'required|string',
            'radius' => 'required|numeric'
        ]);

        $lokasi = $request->lokasi;
        $radius = $request->radius;

        $update = DB::table('config_lokasi')->where('id_config_lokasi', 1)->update([
            'lokasi' => $lokasi,
            'radius' => $radius
        ]);

        if ($update) {
            return Redirect::back()->with(['success'=>'Data Berhasil DiUpdate']);
        } else {
            return Redirect::back()->with(['warning'=>'Data Gagal DiUpdate']);
        }
    }

    public function jamKerja()
    {
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();

        // Jika belum ada data, buat nilai default kosong agar tidak error
        if (!$jamkerja) {
            $jamkerja = (object)[
                'awal_j_masuk' => '',
                'j_masuk' => '',
                'akhir_j_masuk' => '',
                'j_pulang' => ''
            ];
        }

        return view('konfigurasi.jamkerja', compact('jamkerja'));
    }

    public function updateJamKerja(Request $request)
    {
        // Tambahkan detik default jika hanya H:i yang dikirim
        foreach (['awal_j_masuk', 'j_masuk', 'akhir_j_masuk', 'j_pulang'] as $field) {
            if (strlen($request->$field) === 5) { // format H:i
                $request->merge([$field => $request->$field . ':00']);
            }
        }
    
        $request->validate([
            'awal_j_masuk' => 'required|date_format:H:i:s',
            'j_masuk' => 'required|date_format:H:i:s|after_or_equal:awal_j_masuk',
            'akhir_j_masuk' => 'required|date_format:H:i:s|after_or_equal:j_masuk',
            'j_pulang' => 'required|date_format:H:i:s|after:akhir_j_masuk',
        ]);
    
        $update = DB::table('jamkerja')->where('id_jamkerja', 1)->update([
            'awal_j_masuk' => $request->awal_j_masuk,
            'j_masuk' => $request->j_masuk,
            'akhir_j_masuk' => $request->akhir_j_masuk,
            'j_pulang' => $request->j_pulang,
        ]);
    
        return Redirect::back()->with([
            $update ? 'success' : 'warning' => $update ? 'Jam Kerja Berhasil Diperbarui' : 'Jam Kerja Gagal Diperbarui'
        ]);
    }
}