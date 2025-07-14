<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\KonfigurasiController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::middleware(['guest:pengajar'])->group( function () {
    // Login page
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
// Proses Login
    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

Route::middleware(['guest:user'])->group( function () {
    // Login page
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
// Proses Login Admin
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

// Mengelompokkan rute yang memerlukan middleware auth:pengajar
Route::middleware(['auth:pengajar'])->group( function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

//presence
    Route::get('/presence/create', [PresenceController::class, 'create']);

    Route::post('/presence/store', [PresenceController::class, 'store']);

//edit profile
    Route::get('/editprofile', [PresenceController::class,'editprofile']);
    Route::post('/presence/{nik}/updateprofile', [PresenceController::class,'updateprofile']);

//histori
    Route::get('/presence/histori', [PresenceController::class,'histori']);
    Route::post('/gethistori', [PresenceController::class,'gethistori']);

//izin
    Route::get('/presence/izin', [PresenceController::class,'izin']);
    Route::get('/presence/formizin', [PresenceController::class,'formizin']);
    Route::post('/presence/storeizin', [PresenceController::class,'storeizin']);
    Route::post('/presence/cekpengajuanizin', [PresenceController::class,'cekpengajuanizin']);

//maps
    Route::get('/presence/map', [PresenceController::class, 'map'])->name('presence.map');

});


Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class,'dashboardadmin']);
    
    //Pengajar
    Route::get('/pengajar', [PengajarController::class, 'index']);
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');
    Route::post('/pengajar/store', [PengajarController::class, 'store']);
    Route::put('/pengajar/update/{nik}', [PengajarController::class, 'update']);
    Route::get('/pengajar/delete/{nik}', [PengajarController::class, 'delete']);
    
    //presence monitoring
    Route::get('/presence/monitoring', [PresenceController::class, 'monitoring']);
    Route::post('/getpresence', [PresenceController::class, 'getpresence']);
    Route::post('/tampilpeta', [PresenceController::class, 'tampilpeta']);
    Route::get('/presence/laporan', [PresenceController::class, 'laporan']);
    Route::post('/presence/cetaklaporan', [PresenceController::class, 'cetaklaporan']);
    Route::get('/presence/rekap', [PresenceController::class, 'rekap']);
    Route::post('/presence/cetakrekap', [PresenceController::class, 'cetakrekap']);
    Route::get('/presence/izinsakit', [PresenceController::class,'izinsakit']);
    Route::post('/presence/approveizinsakit', [PresenceController::class, 'approveizinsakit']);
    Route::get('/presence/{id}/batalkanizinsakit', [PresenceController::class,'batalkanizinsakit']);
    
    
    //konfigurasi
    Route::get('/konfigurasi/lokasi', [KonfigurasiController::class, 'lokasi']);
    Route::post('/konfigurasi/updatelokasi', [KonfigurasiController::class, 'updatelokasi']);
    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);


});