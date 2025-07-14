@extends('layouts.presence')
@section('content')
<div class="section" id="user-section">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem;">
                <div style="display: flex; align-items: center;">
                    <div class="avatar" style="margin-right: 12px;">
                        @php
                            $user = Auth::guard('pengajar')->user();
                            $defaultFoto = asset('assets/img/sample/avatar/avatar1.jpg');
                            $fotoPath = storage_path('app/public/uploads/pengajar/' . $user->foto);
                            $fotoUrl = asset('storage/uploads/pengajar/' . $user->foto);
                        @endphp

                        <div class="avatar" style="margin-right: 12px;">
                            @if (!empty($user->foto) && file_exists($fotoPath))
                                <img src="{{ $fotoUrl }}" alt="avatar" class="imaged w64 rounded" style="height:60px">
                            @else
                                <img src="{{ $defaultFoto }}" alt="avatar" class="imaged w64 rounded" style="height:60px">
                            @endif
                        </div>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 18px; color: white;">{{ Auth::guard('pengajar')->user()->nama_lengkap }}</h2>
                        <span style="color: white;">{{ Auth::guard('pengajar')->user()->jabatan }}</span>
                    </div>
                </div>
                <a href="/proseslogout" class="btn btn-sm text-white" style="margin-left: auto;">
                    <ion-icon name="log-out-outline"></ion-icon> Logout
                </a>
            </div>
        </div>
        <div class="section" id="menu-section">
            <div class="card">
                <div class="card-body text-center">
                    <div class="list-menu">
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/editprofile" class="green" style="font-size: 40px;">
                                    <ion-icon name="person-sharp"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Profil</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="presence/izin" class="danger" style="font-size: 40px;">
                                    <ion-icon name="calendar-number"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Izin</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="{{ url('presence/histori') }}" class="warning" style="font-size: 40px;">
                                    <ion-icon name="document-text"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                <span class="text-center">Histori</span>
                            </div>
                        </div>
                        <div class="item-menu text-center">
                            <div class="menu-icon">
                                <a href="/presence/map" class="orange" style="font-size: 40px;">
                                    <ion-icon name="location"></ion-icon>
                                </a>
                            </div>
                            <div class="menu-name">
                                Lokasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section mt-2" id="presence-section">
            <div class="todaypresence">
                <div class="row">
                    <div class="col-6">
                        <div class="card gradasigreen">
                            <div class="card-body">
                                <div class="presencecontent -flex align-items-center" style="gap: 12px;">
                                    <div class="iconpresence">
                                            @if ($presencehariini <> null )
                                            @php
                                                $path = Storage::url('uploads/presence/'.$presencehariini->foto_in);
                                            @endphp
                                                <img src="{{ url($path) }}" alt="" class="imaged w48">
                                            @else
                                                <ion-icon name="camera" style="font-size: 32px;"></ion-icon>
                                            @endif
                                        </div>
                                    <div class="presencedetail">
                                        <h4 class="presencetitle">Masuk</h4>
                                        <span class="small text">{{ $presencehariini <> null ? $presencehariini->jam_in : 'Belum Absen' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card gradasired">
                            <div class="card-body">
                                <div class="presencecontent -flex align-items-center" style="gap: 12px;">
                                    <div class="iconpresence">
                                            @if ($presencehariini <> null && $presencehariini->jam_out <> null )
                                            @php
                                                $path = Storage::url('uploads/presence/'.$presencehariini->foto_in);
                                            @endphp
                                                <img src="{{ url($path) }}" alt="" class="imaged w48">
                                            @else
                                                <ion-icon name="camera"></ion-icon>
                                            @endif
                                        </div>
                                    <div class="presencedetail">
                                        <h4 class="presencetitle">Pulang</h4>
                                        <span class="small text">{{ $presencehariini <> null && $presencehariini->jam_out <> null ? $presencehariini->jam_out : 'Belum Absen' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!-- * template grafik taro disini -->
        <div id="rekappresence">
            <h3>Rekapitulasi Absensi Bulan {{ $namabulan[$bulanini] }} {{ $tahunini }}</h3>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekappresence ->jmlhadir }}</span>
                            <ion-icon name="checkbox-outline" style="font-size: 1.6rem;" class="text-primary mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekapizin->jmlizin }}</span>
                            <ion-icon name="document-text-outline" style="font-size: 1.6rem;" class="text-warning mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Izin</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekapizin->jmlsakit }}</span>
                            <ion-icon name="medkit-outline" style="font-size: 1.6rem;" class="text-success mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 12px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; font-size:0.6rem; z-index:999">{{ $rekappresence ->jmltelat }}</span>
                            <ion-icon name="alarm-outline" style="font-size: 1.6rem;" class="text-danger mb-1"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500">Telat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="presencetab mt-2">
                <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                    <ul class="nav nav-tabs style1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                                Bulan Ini
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                                Daftar Hadir Hari Ini
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content mt-2" style="margin-bottom:100px;">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <!--
                        <ul class="listview image-listview">
                            @foreach ($historibulanini as $d)
                                <li>
                                    <div class="item">
                                        <div class="icon-box bg-primary">
                                            <ion-icon name="finger-print-outline"></ion-icon>
                                        </div>
                                        <div class="in">
                                            <div>{{ date("d-m-Y",strtotime($d->tgl_presence)) }}</div>
                                            <span class="badge badge-success">{{ $d->jam_in }}</span>
                                            <span class="badge badge-danger">{{ $presencehariini <> null && $d->jam_out <> null ? $d->jam_out : 'Belum Absen' }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            
                        </ul>
                    -->
                    <style>
                        .historicontent {
                            display: flex;
                        }

                        .datapresence {
                            margin-left: 10px;
                        }
                    </style>
                    @foreach ($historibulanini as $d)
                    <div class="card">
                        <div class="card-body">
                            <div class="historicontent">
                                <div class="iconpresence">
                                    <ion-icon name="finger-print-outline" style="font-size: 48px"; class="text-success"></ion-icon>
                                </div>
                                <div class="datapresence">
                                    <h3 style="line-height: 3px">{{ $d->namahari }}</h3>
                                    <h4 style="margin: 0px !important">{{ date("d-m-Y",strtotime($d->tgl_presence)) }}</h4>
                                    <span>
                                        {!! $d->jam_in <> null ? date("H:i",strtotime($d->jam_in)) : '<span
                                        class="text-danger">Belum Absen</span>'!!}
                                    </span>
                                    <span>
                                        {!! $d->jam_out <> null ? "-". date("H:i",strtotime($d->jam_out)) : '<span
                                        class="text-danger">- Belum Absen</span>'!!}
                                    </span>
                                    <br>
                                    <span>
                                        {!! date("H:i",strtotime($d->jam_in)) > date("H:i",strtotime($d->j_masuk)) ? '<span
                                        class="text-danger">Terlambat</span>' : '<span class="text-success">Tepat Waktu</span>' !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <ul class="listview image-listview">
                            @foreach ($daftarhadirhariini as $d)
                            <li>
                                <div class="d-flex align-items-center p-3" style="background-color: #fff; border-radius: 8px;">
                                    @if (!empty($d->foto))
                                        @php
                                            $path = Storage::url('uploads/pengajar/' . $d->foto);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="avatar" class="rounded-circle" style="width:60px; height:60px; margin-right: 15px;">
                                    @else
                                        <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="rounded-circle" style="width:60px; height:60px; margin-right: 15px;">
                                    @endif

                                    <div>
                                        <b style="font-size: 16px;">{{ $d->nama_lengkap }}</b><br>
                                        <small class="text-muted" style="font-size: 14px;">{{ $d->jabatan }}</small>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
@endsection