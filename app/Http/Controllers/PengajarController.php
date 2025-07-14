<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Pengajar; 

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pengajar')->orderBy('nama_lengkap');

        if ($request->has('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        $pengajar = $query->paginate(10);

        return view('pengajar.index', compact('pengajar'));
    }
    
    public function store(Request $request)
    {
        $nik           = $request->nik;
        $nama_lengkap  = $request->nama_lengkap;
        $jabatan       = $request->jabatan;
        $no_hp         = $request->no_hp;
        $password      = Hash::make('123'); // password default
        $rememberToken = Str::random(60);

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = 'assets/img/nofoto.png'; // default foto
        }

        try {
            $data = [
                'nik'             => $nik,
                'nama_lengkap'    => $nama_lengkap,
                'jabatan'         => $jabatan,
                'no_hp'           => $no_hp,
                'foto'            => $foto,
                'password'        => $password,
                'remember_token'  => $rememberToken
            ];

            $simpan = DB::table('pengajar')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/pengajar/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success'=>'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) {
                $message = "Data dengan Nik " . $nik . " Sudah Ada";
            }
            return Redirect::back()->with(['warning'=>'Data Gagal Disimpan: '.$message]);
        }
    }
    
    public function update(Request $request, $nik)
    {
        $request->validate([
            'nik' => 'required|unique:pengajar,nik,' . $nik . ',nik',
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'no_hp' => 'required'
        ]);

        $pengajar = Pengajar::where('nik', $request->nik_lama)->firstOrFail();

        $pengajar->nik = $request->nik;
        $pengajar->nama_lengkap = $request->nama_lengkap;
        $pengajar->jabatan = $request->jabatan;
        $pengajar->no_hp = $request->no_hp;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('uploads/pengajar', 'public');
            $pengajar->foto = basename($foto);
        }

        $pengajar->save();

        return redirect()->route('pengajar.index')->with('success', 'Data Berhasil Diperbaharui.');
    }
    
    public function delete($nik)
    {
        $pengajar = Pengajar::where('nik', $nik)->first();
        if ($pengajar) {
            // Hapus foto jika ada
            if ($pengajar->foto && file_exists(storage_path('app/public/uploads/pengajar/' . $pengajar->foto))) {
                unlink(storage_path('app/public/uploads/pengajar/' . $pengajar->foto));
            }
            $pengajar->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->back()->with('warning', 'Data tidak ditemukan.');
        }
    }

}