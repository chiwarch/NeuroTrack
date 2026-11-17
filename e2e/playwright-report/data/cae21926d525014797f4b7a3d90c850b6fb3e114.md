# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 02-rate-kehadiran.spec.ts >> Rate kehadiran >> dashboard guru menandai hari yang belum difinalisasi
- Location: tests\02-rate-kehadiran.spec.ts:24:3

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: getByTestId('unfinalized-warning')
Expected: visible
Timeout: 8000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" getByTestId('unfinalized-warning') with timeout 8000ms
  - waiting for getByTestId('unfinalized-warning')

```

```yaml
- complementary:
  - link "NeuroTrack":
    - /url: http://127.0.0.1:8000
  - navigation:
    - link "Beranda":
      - /url: http://127.0.0.1:8000/guru
    - link "Kuis Saya":
      - /url: http://127.0.0.1:8000/guru/kuis
    - link "Tugas":
      - /url: http://127.0.0.1:8000/guru/tugas
    - link "Pengumuman":
      - /url: http://127.0.0.1:8000/guru/pengumuman
    - link "Penghargaan":
      - /url: http://127.0.0.1:8000/guru/penghargaan
    - link "Peringkat Kelas":
      - /url: http://127.0.0.1:8000/guru/peringkat
    - link "Absensi":
      - /url: http://127.0.0.1:8000/guru/absensi
    - link "Penilaian Siswa":
      - /url: http://127.0.0.1:8000/guru/siswa
  - link "GR Guru RPL E2E Guru":
    - /url: http://127.0.0.1:8000/profil
  - button "Keluar"
- main:
  - heading "Halo, Guru RPL E2E" [level=1]
  - paragraph: Kelola kuis untuk mata pelajaran yang kamu ampu.
  - link "Buat Kuis":
    - /url: http://127.0.0.1:8000/guru/kuis/create
  - paragraph: "0"
  - paragraph: Kuis
  - paragraph: "0"
  - paragraph: Diterbitkan
  - paragraph: "0"
  - paragraph: Total Soal
  - paragraph: "0"
  - paragraph: Pengerjaan Selesai
  - heading "Mapel yang Diampu" [level=2]
  - paragraph: Bahasa Inggris E2E
  - paragraph: Umum
  - paragraph: DPK-RPL
  - paragraph: Kejuruan
  - paragraph: Matematika E2E
  - paragraph: Umum
  - paragraph: Sejarah
  - paragraph: Umum
  - heading "Pengerjaan Terbaru" [level=2]
  - paragraph: Belum ada siswa yang menyelesaikan kuismu.
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import { ACCOUNTS, NIS, siswa } from '../fixtures/accounts';
  3  | import { login } from '../helpers/auth';
  4  | 
  5  | // PRD 3.5.4: Rate = (E - T) / E x 100. Dispensasi, PKL, terlambat tidak mengurangi.
  6  | test.describe('Rate kehadiran', () => {
  7  |   test('contoh PRD: 60 efektif, 2 dispensasi, S2 I1 A3 -> 89,7%', async ({ page }) => {
  8  |     await login(page, siswa(NIS.rateExample));
  9  |     await expect(page.getByTestId('rate-value')).toHaveText(/89,7\s*%/);
  10 |     await expect(page.getByTestId('rate-formula')).toBeVisible(); // rumus transparan
  11 |   });
  12 | 
  13 |   test('siswa PKL berbulan-bulan tidak menurunkan rate', async ({ page }) => {
  14 |     await login(page, siswa(NIS.pkl));
  15 |     await expect(page.getByTestId('rate-value')).toHaveText(/100(,0)?\s*%/);
  16 |   });
  17 | 
  18 |   test('terlambat tidak mengurangi rate, tetapi dicatat terpisah', async ({ page }) => {
  19 |     await login(page, siswa(NIS.late));
  20 |     await expect(page.getByTestId('rate-value')).toHaveText(/100(,0)?\s*%/);
  21 |     await expect(page.getByTestId('late-count')).toHaveText(/5/);
  22 |   });
  23 | 
  24 |   test('dashboard guru menandai hari yang belum difinalisasi', async ({ page }) => {
  25 |     await login(page, ACCOUNTS.guruRpl);
> 26 |     await expect(page.getByTestId('unfinalized-warning')).toBeVisible();
     |                                                           ^ Error: expect(locator).toBeVisible() failed
  27 |   });
  28 | });
  29 | 
```