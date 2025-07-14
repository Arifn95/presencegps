<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Models\Pengajuanizin;

class PresenceController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nik = Auth::guard('pengajar')->user()->nik;
        $cek = DB::table('presence')->where('tgl_presence', $hariini)->where('nik', $nik)->count();
        $con_lokasi = DB::table('config_lokasi')->where('id_config_lokasi', 1)->first();
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();
        return view('presence.create', compact('cek','con_lokasi','jamkerja'));
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('pengajar')->user()->nik;
        $tgl_presence = date("Y-m-d");
    
        // Ambil konfigurasi lokasi
        $con_lokasi = DB::table('config_lokasi')->where('id_config_lokasi', 1)->first();
        $lok = explode(",", $con_lokasi->lokasi);
        $latitudesekolah = $lok[0];
        $longitudesekolah = $lok[1];
    
        // Lokasi user
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
    
        // Hitung jarak user dari sekolah
        $jarak = $this->distance($latitudesekolah, $longitudesekolah, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);
    
        // Cek apakah sudah absen hari ini
        $cek = DB::table('presence')->where('tgl_presence', $tgl_presence)->where('nik', $nik)->count();
        $ket = $cek > 0 ? "out" : "in";
    
        // Ambil jam kerja
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();
    
        // Ambil waktu sekarang
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $jam = $now->format('H:i:s');
    
        // Handle file image
        $image = $request->image;
        $folderPath = "public/uploads/presence/";
        $formatName = $nik . "-" . $tgl_presence . "-" . $ket;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;
    
        // Jika user berada di luar radius
        if ($radius > $con_lokasi->radius) {
            return response()->json([
                'status' => 1,
                'radius' => $radius
            ]);
        }
    
        // === Absen Pulang ===
        if ($cek > 0) {
            // Validasi: tidak boleh absen pulang sebelum jam pulang
            if (isset($jamkerja->j_pulang) && $jam < $jamkerja->j_pulang) {
                return response()->json([
                    'status' => 2
                ]);
            }
    
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'location_out' => $lokasi
            ];
    
            $update = DB::table('presence')
                ->where('tgl_presence', $tgl_presence)
                ->where('nik', $nik)
                ->update($data_pulang);
    
            if ($update) {
                Storage::put($file, $image_base64);
                return response()->json(['status' => 0, 'tipe' => 'pulang']);
            } else {
                return response()->json(['status' => 1]);
            }
        }
    
        // === Absen Masuk ===
    
        // Ambil dan parse jam masuk
        $awal_j_masuk = \Carbon\Carbon::parse($jamkerja->awal_j_masuk);
        $j_masuk = \Carbon\Carbon::parse($jamkerja->j_masuk);
        $akhir_j_masuk = \Carbon\Carbon::parse($jamkerja->akhir_j_masuk);
    
        // Terlalu cepat
        if ($now->lt($awal_j_masuk)) {
            return response()->json([
                'status' => 3,
            ]);
        }
    
        // Terlambat melebihi batas akhir
        if ($now->gt($akhir_j_masuk)) {
            return response()->json([
                'status' => 4,
            ]);
        }
    
        // Jika masih dalam rentang jam masuk (tepat waktu atau terlambat)
        $data = [
            'nik' => $nik,
            'tgl_presence' => $tgl_presence,
            'jam_in' => $jam,
            'foto_in' => $fileName,
            'location_in' => $lokasi
        ];
    
        $simpan = DB::table('presence')->insert($data);
    
        if ($simpan) {
            Storage::put($file, $image_base64);
            $tipe = $now->gt($j_masuk) ? 'terlambat' : 'masuk';
            return response()->json(['status' => 0, 'tipe' => $tipe]);
        } else {
            return response()->json(['status' => 1]);
        }
    }
    


    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nik = Auth::guard('pengajar')->user()->nik;
        $pengajar = DB::table('pengajar')->where('nik', $nik)->first();
        return view('presence.editprofile', compact('pengajar'));
    }

    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('pengajar')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $passwordInput = $request->password;
        $pengajar = DB::table('pengajar')->where('nik', $nik)->first();

		if ($request->hasFile('foto')) {
			// Validasi foto lama hanya jika ada
			if (!empty($pengajar->foto)) {
				$oldFotoPath = storage_path('app/public/uploads/pengajar/' . $pengajar->foto);

				if (file_exists($oldFotoPath)) {
					@unlink($oldFotoPath); // pakai @ biar suppress error jika file tidak bisa dihapus
				}
			}

			// Simpan foto baru ke storage
			$ext = $request->file('foto')->getClientOriginalExtension();
			$foto = $nik . "." . $ext;
			$request->file('foto')->storeAs('public/uploads/pengajar', $foto);
		} else {
			$foto = $pengajar->foto;
        }

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_hp' => $no_hp,
            'foto' => $foto
        ];

        if (!empty($passwordInput)) {
            $data['password'] = Hash::make($passwordInput);
        }

        $update = DB::table('pengajar')->where('nik', $nik)->update($data);

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/pengajar/";
                // Resize and save image using Intervention Image
                $image = Image::make($request->file('foto'))
                    ->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio(); // pertahankan rasio
                        $constraint->upsize();      // jangan diperbesar dari ukuran aslinya
                    })
                    ->resizeCanvas(300, 300, 'center', false, '#ffffff')
                    ->encode();

                Storage::disk('public')->put('uploads/pengajar/' . $foto, $image);
            }
            return Redirect::back()->with('success', 'Profil Berhasil Diperbarui');
        } else {
            return Redirect::back()->with('error', 'Data Gagal Diperbarui');
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presence.histori', compact('namabulan'));
    }

    public function gethistori(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('pengajar')->user()->nik;

        $histori = DB::table('presence')
            ->whereRaw('MONTH(tgl_presence)="' .$bulan . '"')
            ->whereRaw('YEAR(tgl_presence)="' .$tahun . '"')
            ->where('nik', $nik)
            ->orderBy('tgl_presence')
            ->get();
        
        return view('presence.gethistori', compact('histori'));
    }

    public function izin()
    {
        $nik = Auth::guard('pengajar')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('nik', $nik)->get();
        return view('presence.izin',compact('dataizin'));
    }

    public function formizin()
    {
        return view('presence.formizin');
    }

    public function storeizin(Request $request)
    {
        
        $nik = Auth::guard('pengajar')->user()->nik;
        $tgl_input = $request->tgl_izin; // ini dari input dd-mm-yyyy
        $tgl_formatted = Carbon::createFromFormat('d-m-Y', $tgl_input)->format('Y-m-d');
        $status = $request->status;
        $ket = $request->ket;

        $data = [
            'nik'=> $nik,
            'tgl_izin'=> $tgl_formatted,
            'status'=> $status,
            'ket'=> $ket
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presence/izin')->with(['success'=>'Data Berhasil Disimpan']);
        } else {
            return redirect('/presence/izin')->with(['error'=>'Data Gagal Disimpan']);
        }
    }
    
    public function monitoring()
    {
        return view('presence.monitoring');
    }
    
    public function getpresence(Request $request)
    {
        $tanggal = $request->tanggal;
        $presence = DB::table('presence')
            ->select('presence.*', 'nama_lengkap')
            ->join('pengajar', 'presence.nik', '=', 'pengajar.nik')
            ->where('tgl_presence', $tanggal)
            ->get();
        
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();

        return view('presence.getpresence', compact('presence', 'jamkerja'));
    }
    
    public function tampilpeta(Request $request)
    {
        $id = $request->id;
        $presence = DB::table('presence')->where('id_presence', $id)
            ->join('pengajar','presence.nik','=','pengajar.nik')
            ->first();
        return view('presence.showmap', compact('presence'));
    }
    
    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $pengajar = DB::table('pengajar')->orderBy('nama_lengkap')->get();
        return view('presence.laporan', compact('namabulan','pengajar'));
    }
    
    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $pengajar = DB::table('pengajar')->where('nik', $nik)->first();
        
        $presence = DB::table('presence')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presence)="'.$bulan.'"')
            ->whereRaw('YEAR(tgl_presence)="'.$tahun.'"')
            ->orderBy('tgl_presence')
            ->get();
        
        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            //fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            //mendefinisikan nama file expor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Absensi $time.xls");
            return view('presence.cetaklaporanexcel', compact('bulan','tahun','namabulan','pengajar','presence', 'jamkerja'));
        }
        return view('presence.cetaklaporan', compact('bulan','tahun','namabulan','pengajar','presence', 'jamkerja'));
    }
    
    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        return view('presence.rekap', compact('namabulan'));
    }
    
    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('presence')
            ->selectRaw('presence.nik,nama_lengkap,
                MAX(IF(DAY(tgl_presence) = 1,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_1,
                MAX(IF(DAY(tgl_presence) = 2,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_2,
                MAX(IF(DAY(tgl_presence) = 3,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_3,
                MAX(IF(DAY(tgl_presence) = 4,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_4,
                MAX(IF(DAY(tgl_presence) = 5,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_5,
                MAX(IF(DAY(tgl_presence) = 6,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_6,
                MAX(IF(DAY(tgl_presence) = 7,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_7,
                MAX(IF(DAY(tgl_presence) = 8,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_8,
                MAX(IF(DAY(tgl_presence) = 9,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_9,
                MAX(IF(DAY(tgl_presence) = 10,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_10,
                MAX(IF(DAY(tgl_presence) = 11,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_11,
                MAX(IF(DAY(tgl_presence) = 12,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_12,
                MAX(IF(DAY(tgl_presence) = 13,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_13,
                MAX(IF(DAY(tgl_presence) = 14,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_14,
                MAX(IF(DAY(tgl_presence) = 15,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_15,
                MAX(IF(DAY(tgl_presence) = 16,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_16,
                MAX(IF(DAY(tgl_presence) = 17,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_17,
                MAX(IF(DAY(tgl_presence) = 18,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_18,
                MAX(IF(DAY(tgl_presence) = 19,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_19,
                MAX(IF(DAY(tgl_presence) = 20,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_20,
                MAX(IF(DAY(tgl_presence) = 21,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_21,
                MAX(IF(DAY(tgl_presence) = 22,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_22,
                MAX(IF(DAY(tgl_presence) = 23,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_23,
                MAX(IF(DAY(tgl_presence) = 24,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_24,
                MAX(IF(DAY(tgl_presence) = 25,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_25,
                MAX(IF(DAY(tgl_presence) = 26,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_26,
                MAX(IF(DAY(tgl_presence) = 27,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_27,
                MAX(IF(DAY(tgl_presence) = 28,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_28,
                MAX(IF(DAY(tgl_presence) = 29,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_29,
                MAX(IF(DAY(tgl_presence) = 30,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_30,
                MAX(IF(DAY(tgl_presence) = 31,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_31')
            ->join('pengajar','presence.nik','=','pengajar.nik')
            ->whereRaw('MONTH(tgl_presence)="'.$bulan.'"')
            ->whereRaw('YEAR(tgl_presence)="'.$tahun.'"')
            ->groupByRaw('presence.nik,nama_lengkap')
            ->get();

        $jamkerja = DB::table('jamkerja')->where('id_jamkerja', 1)->first();
        
        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            //fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            //mendefinisikan nama file expor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekapitulasi Absensi $time.xls");
        }
        return view('presence.cetakrekap', compact('bulan','tahun','namabulan','rekap','jamkerja'));
    }
    
    public function izinsakit(Request $request)
    {
        
        $query = Pengajuanizin::query();
        $query->select('id_pengajuan_izin','tgl_izin','pengajuan_izin.nik','nama_lengkap','jabatan','status','ket','stat_aprvd');
        $query->join('pengajar','pengajuan_izin.nik','=','pengajar.nik');
        if(!empty($request->dari) && !empty($request->sampai)){
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }
        if(!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', [$request->nik]);
        }

        if(!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap','like','%'. $request->nama_lengkap .'%');
        }

        if($request->stat_aprvd ==='0' || $request->stat_aprvd ==='1' || $request->stat_aprvd ==='2') {
            $query->where('stat_aprvd',$request->stat_aprvd);
        }
        $query->orderBy('tgl_izin','desc');
        $izinsakit = $query->paginate(2);
        $izinsakit->appends($request->all());
        return view('presence.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request)
    {
        $stat_aprvd = $request->stat_aprvd;
        $id_izinsakit_form = $request->id_izinsakit_form;
        $update = DB::table('pengajuan_izin')->where('id_pengajuan_izin',$id_izinsakit_form)->update(['stat_aprvd'=>$stat_aprvd]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id_pengajuan_izin',$id)->update(['stat_aprvd'=>0]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $tgl_db = Carbon::createFromFormat('d-m-Y', $tgl_izin)->format('Y-m-d');

        $nik = Auth::guard('pengajar')->user()->nik;

        $jumlah = DB::table('pengajuan_izin')
            ->where('nik', $nik)
            ->where('tgl_izin', $tgl_db)
            ->count();

        return response($jumlah); // return angka langsung, bukan JSON
    }

    public function map()
    {
        $con_lokasi = DB::table('config_lokasi')->where('id_config_lokasi', 1)->first();

        return view('presence.map', compact('con_lokasi'));
    }

}