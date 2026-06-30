# Aplikasi Operational Record & Auto-Generate Horenzo

Tech Stack: **Laravel + MySQL + Tailwind CSS**

## 1. Latar Belakang & Tujuan

Saat ini pencatatan Operational Record dilakukan manual per proses produksi (Manual Bending, Auto Bending, Shape Check Jig, Drawing & Inspection). Data ini perlu direkap berkala menjadi laporan Horenzo, yang saat ini disusun manual sehingga rawan human error dan memakan waktu.

Tujuan aplikasi:

- Digitalisasi pencatatan Operational Record per karyawan/inspector, menggantikan pencatatan manual.
- Generate otomatis laporan Horenzo (rekap/summary) dari data Operational Record, tanpa rekap manual ulang.
- Konsistensi data lintas proses dengan struktur Header & Body yang seragam.
- Audit trail jelas: siapa input, kapan, proses apa, dan hasil produksi seperti apa.

## 2. Scope

### Termasuk
- Input Operational Record untuk 4 proses: Manual Bending, Auto Bending, Shape Check Jig, Drawing & Inspection.
- Master data: Karyawan/NIK, Part Code, LOT Number, Process Main, Code Aktivitas.
- Auto-generate laporan Horenzo berbasis filter periode/shift/proses/part.
- Role & permission dasar (Operator/Inspector, Admin/Leader).
- Export laporan (PDF/Excel) untuk Operational Record maupun Horenzo.

### Di Luar Scope (Fase Awal)
- Integrasi langsung ke mesin Auto Bending (IoT/PLC) — input tetap manual.
- Notifikasi otomatis (email/WA) — fase berikutnya.
- Aplikasi mobile native — fase awal web responsif (Tailwind).

## 3. Aktor & Role

| Role | Deskripsi | Hak Akses Utama |
|---|---|---|
| Operator (Bending) | Karyawan Manual/Auto Bending | Input Operational Record proses Bending, sign digital (Prepare) |
| Inspector | Karyawan Shape Check Jig & Drawing/Inspection | Input Operational Record proses tsb |
| Leader/Admin | Penanggung jawab line/produksi | Kelola master data, lihat & generate Horenzo, approve/verifikasi record |
| Superadmin | IT/Sistem | Kelola user, role, konfigurasi master data global |

## 4. Alur Proses Bisnis

1. Operator/Inspector login sesuai role.
2. Pilih Process Main (Manual Bending / Auto Bending / Shape Check Jig / Drawing & Inspection) untuk buat Operational Record baru.
3. Isi **Header**: Date, Shift, Process Main, Process 2, NIK (bisa >1), Prepare (signature).
4. Isi satu/banyak baris **Body**: Part Code, LOT Number, Code, Start Time, End Time, Min (auto), QTY, NG, Hold, Remark.
5. Sistem auto-hitung `Min` dari Start/End Time, validasi numerik QTY/NG/Hold.
6. Record tersimpan status **Draft**, lalu submit jadi **Final** per shift.
7. Leader/Admin lihat seluruh record lintas proses & karyawan.
8. Leader/Admin generate **Horenzo** dengan filter (periode, shift, process, part, dst) — sistem rekap otomatis dari Body.
9. Horenzo diekspor ke PDF/Excel.

## 5. Struktur Data Operational Record

> Keempat proses memakai struktur Header & Body **identik** — perbedaan hanya nilai `Process Main`. Body terikat ke Header (bukan per-NIK), karena pencatatan dilakukan oleh satu pencatat (umumnya Inspector) per Header meski NIK di Header bisa lebih dari satu.

### 5.1 Header

| Field | Tipe Input | Keterangan |
|---|---|---|
| Date | Date picker | Tanggal pelaksanaan |
| Shift | Dropdown | Shift kerja (master data) |
| Process Main | Dropdown | Manual Bending / Auto Bending / Shape Check Jig / Drawing & Inspection |
| Process 2 | Textbox (free text) | Total proses karyawan; dinamis, bukan kategori |
| NIK | Multi-select / tag input | Bisa >1 NIK per Header |
| Prepare | Signature digital | Tanda tangan penulis Operational Record |

### 5.2 Body (1 Header → banyak baris Body)

| Field | Tipe Input | Keterangan |
|---|---|---|
| Part Code | Textbox | Kode part yang dikerjakan |
| LOT Number | Dropdown (kategori) | Format `P2507` = Product, Tahun 25, Bulan 07. Master dikelola Admin |
| Code | Dropdown | Kode aktivitas 0-9 |
| Start Time | Time picker | Jam mulai |
| End Time | Time picker | Jam selesai |
| Min | Auto-calculated | Durasi = End Time − Start Time, read-only |
| QTY | Textbox numerik | Wajib angka |
| NG | Textbox numerik | Wajib angka |
| Hold | Textbox numerik | Wajib angka |
| Remark | Textbox | Keterangan aktivitas |

### 5.3 Master Code Aktivitas

| Code | Aktivitas |
|---|---|
| 1 | Taisou |
| 2 | Briefing |
| 3 | Preparation Prod |
| 4 | Running |
| 5 | Trial |
| 6 | Maintenance |
| 7 | STOP |
| 8 | Change Model |
| 9 | ART-4S |
| 0 | No Plan |

### 5.4 Master LOT Number

Pola `P{YY}{MM}`, contoh `P2507` = Product, tahun 2025, bulan 07.

- Auto-generate opsi LOT bulan berjalan tiap pergantian bulan.
- Admin tetap bisa tambah/nonaktifkan LOT manual (mis. lintas periode/koreksi).
- LOT ditampilkan sebagai dropdown di Body form (bukan free text) agar konsisten untuk rekap Horenzo.

## 6. Fitur Auto-Generate Horenzo

Horenzo = laporan rekap/summary digenerate otomatis dari kumpulan data Body, tanpa input ulang.

**Filter:**
- Rentang tanggal dan/atau Shift
- Process Main (salah satu/semua)
- Part Code dan/atau LOT Number
- NIK/Karyawan tertentu (opsional)

**Output:**
- Total QTY, NG, Hold per Part Code / LOT / Process Main
- Breakdown durasi (menit) per Code aktivitas (analisa downtime: Running vs STOP vs Maintenance)
- Rekap per NIK/karyawan (jam kerja per kategori aktivitas)
- Grafik ringkas (bar/pie) komposisi aktivitas & tren NG per periode
- Export ke Excel (tabel) dan PDF (siap cetak)

**Logika perhitungan:** query agregasi (SUM/COUNT/AVG) terhadap tabel Body, join ke Header untuk filter Date/Shift/Process Main/NIK, disimpan sebagai snapshot laporan (histori laporan tidak berubah meski Operational Record direvisi setelahnya).

## 7. Arsitektur Sistem

- **Laravel** sebagai backend MVC + Blade, atau **Livewire** untuk form dinamis (add-row Body, multi-select NIK tanpa reload).
- **MySQL**: struktur Header-Body (1-to-many) seragam lintas 4 proses — single table dengan kolom `process_main`, bukan tabel terpisah per proses, agar Horenzo bisa query lintas proses dengan mudah.
- **Tailwind CSS**: komponen form reusable (dropdown, signature pad, time picker, numeric input) karena layout sama persis di semua proses.
- **Auth**: Laravel Breeze/Sanctum + **Spatie Laravel-Permission** untuk role-based access.

### 7.1 Skema Database (Ringkas)

| Tabel | Kolom Kunci | Keterangan |
|---|---|---|
| `users` | id, name, nik, role_id | Akun login |
| `op_record_headers` | id, date, shift_id, process_main, process_2, prepare_signature, status, created_by | 1 Header = 1 sesi pencatatan |
| `op_record_header_nik` (pivot) | header_id, user_id/nik | Many-to-many Header ↔ NIK |
| `op_record_bodies` | id, header_id, part_code, lot_id, code_id, start_time, end_time, duration_min, qty, ng, hold, remark | 1 Header → banyak Body |
| `m_lot_numbers` | id, code (P2507), year, month, is_active | Master LOT, auto-generate per bulan |
| `m_activity_codes` | id (0-9), name | Master Code aktivitas |
| `m_part_codes` | id, code, name | Master Part Code (opsional, atau tetap free text) |
| `horenzo_reports` | id, filter_params(json), generated_by, generated_at, snapshot_data(json) | Snapshot hasil generate Horenzo |

> `Process Main` disimpan sebagai kolom enum/lookup di `op_record_headers` (bukan tabel terpisah) karena layout 4 proses identik.

### 7.2 Modul Aplikasi

- Autentikasi & Role Management
- Operational Record (Create/Edit/View, dynamic add-row Body)
- Master Data (LOT, Activity Code, Part Code, User/NIK, Shift)
- Horenzo Generator (filter → preview → export)
- Dashboard (ringkasan harian: total record masuk, total NG, alert proses STOP/Maintenance tinggi)

## 8. Validasi & Aturan Bisnis

- QTY, NG, Hold wajib numerik dan ≥ 0.
- End Time > Start Time dalam baris Body yang sama; `Min` dihitung otomatis, read-only.
- Minimal 1 NIK wajib per Header; minimal 1 baris Body sebelum Header bisa Final.
- Header status **Final** tidak bisa diedit langsung oleh Operator/Inspector — revisi perlu approval Leader/Admin (audit log tercatat).
- LOT Number hanya dari master aktif (`is_active = true`) sesuai bulan berjalan, kecuali Admin buka LOT periode lalu manual.

## 9. Non-Functional Requirements

- Responsive design (Tailwind) — form nyaman diisi dari tablet/HP di lantai produksi.
- Performance — generate Horenzo 1 bulan data selesai < 5 detik (index pada `date`, `process_main`, `lot_id`).
- Audit log setiap create/update/delete pada Header & Body (user, timestamp, perubahan).
- Backup data berkala (data ini jadi dasar laporan resmi produksi).

## 10. Roadmap Implementasi

1. **Fase 1** — Master data (User/NIK, LOT, Activity Code) + Modul Operational Record (CRUD Header & Body) untuk 4 proses.
2. **Fase 2** — Modul Horenzo Generator (filter, rekap, export PDF/Excel).
3. **Fase 3** — Dashboard & analitik (tren NG, downtime per Code aktivitas).
4. **Fase 4 (opsional)** — Approval workflow, notifikasi, integrasi mesin.

## 11. Pertanyaan Terbuka

- Apakah `Shift` master data tetap (Shift 1/2/3) atau dikelola dinamis oleh Admin?
- Apakah Part Code perlu distandarkan ke master data, atau tetap free-text sesuai requirement awal?
- Apakah perlu approval/verifikasi berjenjang sebelum Header Final, atau cukup self-submit oleh pencatat?
- Format export Horenzo mengikuti template existing perusahaan? (perlu contoh format kalau ada)
