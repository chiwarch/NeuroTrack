# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 02-rate-kehadiran.spec.ts >> Rate kehadiran >> terlambat tidak mengurangi rate, tetapi dicatat terpisah
- Location: tests\02-rate-kehadiran.spec.ts:18:3

# Error details

```
Error: expect(locator).toHaveText(expected) failed

Locator: getByTestId('rate-value')
Expected pattern: /100(,0)?\s*%/
Timeout: 8000ms
Error: element(s) not found

Call log:
  - Expect "toHaveText" getByTestId('rate-value') with timeout 8000ms
  - waiting for getByTestId('rate-value')

```

```yaml
- complementary:
  - link "NeuroTrack":
    - /url: http://127.0.0.1:8000
  - navigation:
    - link "Beranda":
      - /url: http://127.0.0.1:8000/siswa
    - link "Katalog Kuis":
      - /url: http://127.0.0.1:8000/siswa/kuis
    - link "Tugas":
      - /url: http://127.0.0.1:8000/siswa/tugas
    - link "Prestasi":
      - /url: http://127.0.0.1:8000/siswa/prestasi
    - link "Peringkat":
      - /url: http://127.0.0.1:8000/siswa/peringkat
    - link "Pengumuman":
      - /url: http://127.0.0.1:8000/siswa/pengumuman
    - link "Statistik":
      - /url: http://127.0.0.1:8000/siswa/statistik
    - link "Absensi":
      - /url: http://127.0.0.1:8000/siswa/absensi
    - link "Riwayat":
      - /url: http://127.0.0.1:8000/siswa/riwayat
  - link "SR Siswa Rate 2026902 Siswa":
    - /url: http://127.0.0.1:8000/profil
  - button "Keluar"
- main:
  - heading "Halo, Siswa! 👋" [level=1]
  - paragraph: Ayo lanjutkan belajarmu hari ini.
  - text: 0%
  - paragraph: Progres Keseluruhan
  - paragraph: 0 dari 0 kuis sudah kamu selesaikan.
  - paragraph: "0"
  - paragraph: Kuis Lulus
  - paragraph: –
  - paragraph: Rata-rata Nilai
  - paragraph: "0"
  - paragraph: Jam Belajar
  - paragraph: "0"
  - paragraph: Hari Berturut-turut
  - heading "Kuis Terbaru" [level=2]
  - link "Lihat Semua":
    - /url: http://127.0.0.1:8000/siswa/kuis
  - text: Belum ada kuis untukmu. Tunggu guru menerbitkannya ya.
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
> 20 |     await expect(page.getByTestId('rate-value')).toHaveText(/100(,0)?\s*%/);
     |                                                  ^ Error: expect(locator).toHaveText(expected) failed
  21 |     await expect(page.getByTestId('late-count')).toHaveText(/5/);
  22 |   });
  23 | 
  24 |   test('dashboard guru menandai hari yang belum difinalisasi', async ({ page }) => {
  25 |     await login(page, ACCOUNTS.guruRpl);
  26 |     await expect(page.getByTestId('unfinalized-warning')).toBeVisible();
  27 |   });
  28 | });
  29 | 
```