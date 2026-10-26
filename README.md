## 📄 License
This project is open for viewing and educational purposes, but **commercial use, redistribution, or selling** of this source code is strictly prohibited without explicit permission. See the [LICENSE](LICENSE) file for details.


# NeuroTrack — Laravel (Tahap 1: Kuis · Tahap 2: Absensi QR)

Platform sekolah SMK: katalog kuis per mapel (umum + kejuruan), dashboard siswa, guru, dan admin.
Tahap berikutnya: absensi, statistik kemampuan, tugas, pengumuman.

## Pasang (Laravel 12 + Vite + Tailwind 4)
```bash
composer create-project laravel/laravel neurotrack
cd neurotrack
# salin SEMUA isi folder ini ke root project (timpa jika diminta):
#   app/  database/  lang/  public/  resources/  routes/
```
Edit `.env`:
```
APP_NAME=NeuroTrack
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
```
Edit `config/app.php`: ubah `'timezone' => 'UTC'` menjadi `'timezone' => 'Asia/Jakarta'`.

```bash
npm install
npm install qrcode             # pustaka pembuat QR (Tahap 2)
php artisan migrate --seed     # tabel + data contoh
composer run dev               # atau: php artisan serve  +  npm run dev
```
Buka http://localhost:8000 — akun contoh (kata sandi `password`):
`admin@neurotrack.test`, `guru@neurotrack.test`, `siswa@neurotrack.test` (atau NIS `10001`).
**Seeder hanya untuk pengembangan; jangan dijalankan di produksi.**

## Alur
- **Admin**: kelola jurusan, mapel (pilih ikon, tugaskan guru), dan pengguna (akun dibuat admin; tidak ada pendaftaran mandiri).
- **Guru**: buat kuis hanya untuk mapel yang diampu, tambah soal (pilihan ganda / isian singkat), terbitkan, lihat hasil per siswa.
- **Siswa**: lihat kuis mapel umum + mapel jurusannya, kerjakan dengan timer, lihat nilai dan pembahasan.

## Keamanan yang sudah ada
Penilaian dilakukan di server (kunci jawaban tidak dikirim ke browser) · timer dicek server · siswa hanya bisa
membuka percobaannya sendiri · guru hanya bisa mengubah kuisnya sendiri · role dicek di setiap route · login dibatasi rate-limit.

## Struktur singkat
`app/Http/Controllers/{Siswa,Guru,Admin}` · `app/Services/QuizGrader.php` · `app/Models` ·
`resources/views/{siswa,guru,admin,sections}` · `resources/css/app.css` (Tailwind) + `landing.css` (gaya landing page).

## Absensi QR (per hari)
- **Admin** → menu *Kelas*: buat kelas, pilih wali kelas, pilih siswa (satu siswa = satu kelas aktif; riwayat keanggotaan tersimpan di `class_memberships`).
- **Wali kelas** → menu *Absensi*: *Mulai absensi hari ini* → tampilkan QR di layar kelas. Status siswa muncul langsung; sakit/izin/dispensasi diatur lewat kolom *Ubah*. *Tutup absensi* menandai siswa yang belum tercatat sebagai **Alfa**.
- **Siswa** memindai QR dengan kamera HP (login dengan akunnya sendiri), menekan *Saya hadir*. Lewat jam batas (default 07:15, kolom `late_after`) = Terlambat.
- QR berganti tiap 20 detik (HMAC dengan `APP_KEY`, sah 20–40 detik) → foto/tautan yang dikirim ke teman cepat kedaluwarsa. `APP_URL` harus alamat yang bisa dibuka dari HP siswa.
- **Rate**: hadir+terlambat ÷ (hadir+terlambat+sakit+izin+alfa) × 100%; dispensasi tidak dihitung. Aturan ada di `app/Services/AttendanceRate.php`.
- Pemetaan ke SQL acuan: `classes`, `class_memberships`, `attendances` (+ kolom `attendance_session_id`, `check_in_at`, `method`, status `dispensation`), tabel baru `attendance_sessions`. Peran di aplikasi tetap `guru`/`siswa` (= `teacher`/`student` di SQL).

## Statistik: radar hard skill & soft skill (Tahap 3)
- **Siswa** → menu *Statistik*: dua radar chart (SVG murni, tanpa library) + grafik batang di bawahnya + penanda "Terkuat" dan "Perlu ditingkatkan".
- **Hard skill** = satu sumbu per mapel (umum + kejuruan jurusannya), nilai 0–100 = rata-rata nilai terbaik per kuis terbit yang sudah dikerjakan. Kuis yang belum dikerjakan tidak dihitung. Aturan ada di `app/Services/SkillStats.php`. Tugas akan ditambahkan sebagai sumber nilai berikutnya.
- **Soft skill** (6 aspek, tabel `skills` kategori `soft_skill`) dinilai guru skala 1–5 lewat menu *Penilaian Siswa*. Satu penilaian per guru per siswa per bulan; nilai akhir = penilaian terbaru tiap guru, dirata-rata. Tabel mengikuti SQL acuan: `skills`, `skill_assessments`, `skill_assessment_scores`.
- Guru dengan mapel umum bisa menilai semua siswa; guru yang hanya mengampu mapel kejuruan hanya siswa jurusannya.
- Di produksi jalankan `php artisan migrate` lalu `php artisan db:seed --class=SkillSeeder` (aman diulang).

## Tugas & guru per kelas (Tahap 4)
- **Admin** → menu *Kelas*: selain siswa dan wali kelas, tentukan **guru pengampu tiap mapel** di kelas itu (tabel `class_subjects`). Guru yang ditugaskan otomatis terdaftar pada mapel tersebut (untuk membuat kuis). Mapel yang sudah punya tugas tidak bisa dilepas.
- **Guru** → menu *Tugas*: buat tugas untuk kelas+mapel yang ia ampu (judul, petunjuk, tenggat, nilai maks), lihat siapa yang sudah mengumpulkan, unduh berkas, beri nilai dan umpan balik.
- **Siswa** → menu *Tugas*: lihat tugas kelasnya, unggah berkas (maks 10 MB) dan/atau jawaban teks. Boleh kumpul ulang selama belum dinilai. Lewat tenggat tetap bisa dikumpulkan tetapi ditandai **Terlambat**.
- Berkas disimpan privat (`storage/app/private/submissions/...`, disk `local`) dan hanya bisa diunduh guru pengampu atau pemilik berkasnya, lewat route yang dicek otorisasinya.
- **Penilaian soft skill** kini dibatasi: guru hanya bisa menilai siswa di kelas yang mapelnya ia ampu.
- **Hard skill** sekarang = rata-rata dari nilai terbaik tiap kuis dan nilai tiap tugas yang sudah dinilai (dalam persen), bobot sama. Aturannya ada di `app/Services/SkillStats.php`.
- Pemetaan ke SQL acuan: `class_subjects`, `assignments`, `submissions` (+ `file_name`, `answer_text`). Hapus `assignments` dibuat RESTRICT agar tidak ada nilai yang hilang diam-diam.

## Perbaikan tampilan ikon mapel & katalog kuis
- Ikon Matematika, B. Indonesia, B. Inggris, dan Seni dan Budaya sebelumnya punya latar kotak-kotak yang menempel sebagai piksel (bukan transparan asli). Sudah dibersihkan menjadi PNG/WebP transparan (`public/images/mapel-*.webp`, 480 px). Ikon RPL diganti dengan versi baru.
- Katalog kuis di halaman depan kini menampilkan satu kuis terbaru **per mapel** (umum dulu, lalu kejuruan), maksimal 6 kartu, 3 kolom.
- Seeder menambah kuis untuk Bahasa Indonesia, Seni dan Budaya, dan Jaringan Dasar (guru: Pak Dimas, `guru2@neurotrack.test`).
- `Subject::iconUrl()` kini memakai ikon buku bila file ikon yang dipilih tidak ada, jadi tidak lagi muncul gambar rusak.
- Jaringan Dasar (TKJ) masih memakai ikon papan klip karena belum ada ikon khususnya.
