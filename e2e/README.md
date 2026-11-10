# NeuroTrack — Suite End-to-End (Playwright)

Menguji alur utama PRD v1.1 (Kriteria Penerimaan Bagian 13 + 6 alur E2E di Prompt 9) dari sisi browser.
Suite ini hitam-kotak: hanya butuh aplikasi berjalan, data seed E2E, dan atribut `data-testid` (kontrak di bawah).

## Menjalankan
```bash
cd e2e && pnpm install && pnpm exec playwright install chromium
cp .env.example .env          # sesuaikan BASE_URL, password, interval QR
pnpm test                     # desktop: alur 1-6 (±3 menit; ada tunggu QR kedaluwarsa)
pnpm test:mobile              # cek label radar di 375 px
pnpm test:load                # 35 siswa memindai bersamaan, P95 < 2 dtk
pnpm report                   # laporan HTML
```
`global-setup.ts` menjalankan `E2E_RESET_CMD` (default: `prisma migrate reset`, yang ikut menjalankan seed) sebelum tiap run.
**Jangan arahkan ke database produksi.**

## Peta ke Kriteria Penerimaan PRD 13
| Kriteria | Test |
|---|---|
| Guru hanya kelas/mapel ditugaskan; ortu anak lain → 403 | 03 (guru tanpa penugasan), 04, 06 |
| QR berganti sesuai interval; token > interval+5 dtk ditolak | 01 "QR berganti", "token kedaluwarsa" |
| Waktu pindai dari server, jam HP tak berpengaruh | 01 "jam HP diubah" |
| Satu catatan per sesi; kelas lain / belum login ditolak | 01 "tercatat; dobel ditolak", "kelas lain", "belum login" |
| Perangkat ganda → "Perlu dicek", tidak ditolak | 01 "perangkat sama dipakai 2 akun" |
| Layar QR tanpa nama siswa | 01 "Layar QR hanya jumlah hadir" |
| Hadir manual wajib alasan; finalisasi; satu sesi harian | 01 (3 test terakhir) |
| Rate 89,7%; dispensasi/PKL/terlambat tak mengurangi; belum final tak dihitung | 02 |
| Nilai berbobot, kunci, revisi + audit lama→baru | 03 |
| Radar X vs XI sumbu berbeda; "belum dinilai"; <3 sumbu → batang; tabel alternatif; semester lalu; 375 px | 05 |
| Radar soft skill 6 sumbu, ×25, sub-indikator, bukti skor 1/4 | 04 |
| Siswa/ortu tanpa rata-rata kelas | 06 |
| Kenaikan kelas + undo | 07 |
| Pindai < 2 dtk P95, ±35 siswa | 08 |

**Belum tercakup** (butuh test terpisah): validasi lokasi (opsional, default mati), import Excel 300 siswa, ekspor Excel/PDF, rekap izin (siswa/ortu ajukan → wali setujui), kunci login 5× gagal, rata-rata kelas hanya bila ≥ 5 siswa terisi.

## Kontrak seed (E2ESeeder)
Password semua akun = `E2E_PASSWORD`. Siswa login dengan NIS.
| Data | Nilai |
|---|---|
| Akun staf | `admin@`, `kepsek@`, `guru.rpl@` (mengajar X RPL 1 + mapel `DPK-RPL`), `guru.tkj@` (tidak mengajar X RPL 1), `wali.xrpl1@`, `ortu.2026001@` (anak: NIS 2026001) — domain `neurotrack.test` |
| X RPL 1 (`x-rpl-1`) | 35 siswa NIS 2026001–2026035; sesi hari ini belum ada; interval QR = `QR_INTERVAL_SECONDS`; NIS 2026003 punya ≥1 sumbu radar belum bernilai |
| NIS 2026101 | siswa X TKJ 1 |
| NIS 2025001 | XI RPL (struktur mapel tanpa Sejarah); NIS 2025950 = XI RPL 1 yang dapat dinaikkan |
| NIS 2026900 | 60 hari efektif, 2 dispensasi, Sakit 2, Izin 1, Alpa 3 (final) |
| NIS 2025901 | PKL 3 bulan; NIS 2026902: 5× terlambat, tanpa alpa |
| NIS 2026910 | jurusan dengan struktur mapel < 3 sumbu hard skill |
| Periode | nilai terbuka (NIS 2026001 belum punya nilai `DPK-RPL`); soft skill terbuka; ada semester lalu untuk poligon pembanding; ada hari belum difinalisasi untuk `guru.rpl` |

## Kontrak `data-testid`
Tambahkan ke UI (bisa lewat prompt ke agent). Pola `<NIS>` = NIS siswa.
| Area | testid |
|---|---|
| Absensi | `start-attendance-<kelas>`, `sessions-today-<kelas>`, `qr-payload` (atribut `data-url` = tautan pindai), `present-count`, `link-pantauan`, `scan-result` |
| Pantauan | `row-<NIS>` berisi `student-name`, `row-status`, `row-flag`, `row-source`, `row-scanned-at` (`data-iso`); `btn-manual`, `manual-reason`, `manual-submit`, `manual-error`, `btn-close-session`, `btn-mark-rest-alpa`, `btn-finalize`, `confirm-finalize`, `session-status` |
| Rate | `rate-value`, `rate-formula`, `late-count`, `unfinalized-warning` |
| Nilai | `grade-<NIS>`, `grade-error-<NIS>`, `final-<NIS>`, `grade-save`, `request-revision-<NIS>`, `revision-new-value`, `revision-reason`, `revision-submit`, `revision-error`, `lock-period`, `confirm-lock`, `approve-revision`, `audit-row` |
| Soft skill | `soft-score-<dim>` (`komunikasi`, `kerja-sama`, `kepemimpinan`, `etos-kerja`, `adaptabilitas`), `soft-score-berpikir-<sub>` (`problem-solving`, `critical-thinking`, `kreativitas`), `soft-evidence-<dim>`, `soft-error-<dim>`, `soft-save`, `soft-saved`, `softradar-axis-value-<key>` |
| Radar hard | `hard-radar-chart`, `hard-bar-chart`, `radar-axis-<key>`, `radar-axis-value-<key>` (teks "belum dinilai" bila null), `radar-axis-label-<key>`, `radar-overlay`, `radar-table-toggle`, `compare-toggle`, `class-average` |
| Umum | `forbidden` (halaman 403), `student-class` |
| Kenaikan kelas | `promo-preview`, `promo-preview-table`, `promo-confirm`, `promo-confirm-yes`, `promo-undo`, `promo-undo-yes` |

Rute yang diasumsikan ada di `fixtures/routes.ts`; ubah di satu tempat bila berbeda.

## Catatan
- Kamera tidak diuji: "memindai" = membuka tautan di QR, jalur yang sama dengan kamera bawaan HP (PRD 3.5.1 langkah 3).
- Test berurutan dan berbagi state; `workers: 1`. Test token kedaluwarsa menunggu `INTERVAL + 6` detik.
- Asumsi mengikuti PRD v1.1 (Next.js, skala soft skill 1–4, tanpa leaderboard). Bila keputusan sudah berubah (mis. skala 1–5), ubah angka ×25 dan nilai di `04-softskill.spec.ts`.
