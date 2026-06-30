@extends('layouts.dashboard')

@section('title', 'Operator Dashboard')
@section('page-title', 'Operator Dashboard')

@section('sidebar')
    <li class="active">
        <a href="{{ route('operator.dashboard') }}"><i class="fas fa-clipboard-list"></i> O/R Header</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-play-circle"></i> Eksekusi Lot</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-history"></i> Riwayat Hari Ini</a>
    </li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-plus-circle text-primary me-2"></i> Buat Operational Record Baru</h5>
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if($errors->any()) <div class="alert alert-danger">Terjadi kesalahan pada input data.</div> @endif
            <form action="{{ route('operator.record.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">Tanggal</label>
                        <input type="text" name="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">Proses</label>
                        <input type="text" name="process" class="form-control" value="Manual Bending" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="Masukkan NIK Anda" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">Nama Operator</label>
                        <input type="text" name="operator_name" class="form-control" placeholder="Masukkan Nama Anda" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">Line</label>
                        <select name="line_id" class="form-select" required>
                            <option value="">-- Pilih Line --</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">Shift</label>
                        <select name="shift_id" class="form-select" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold py-2"><i class="fas fa-save me-2"></i> Simpan & Mulai Lot</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
