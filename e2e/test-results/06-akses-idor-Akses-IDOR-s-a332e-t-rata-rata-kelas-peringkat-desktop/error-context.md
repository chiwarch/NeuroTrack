# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 06-akses-idor.spec.ts >> Akses & IDOR >> siswa dan ortu tidak melihat rata-rata kelas / peringkat
- Location: tests\06-akses-idor.spec.ts:30:3

# Error details

```
Error: expect(locator).toHaveCount(expected) failed

Locator:  getByText(/peringkat|leaderboard/i)
Expected: 0
Received: 1
Timeout:  8000ms

Call log:
  - Expect "toHaveCount" getByText(/peringkat|leaderboard/i) with timeout 8000ms
  - waiting for getByText(/peringkat|leaderboard/i)
    19 × locator resolved to 1 element
       - unexpected value "1"

```

# Page snapshot

```yaml
- generic [ref=f1e2]:
  - complementary [ref=f1e3]:
    - link "NeuroTrack" [ref=f1e4] [cursor=pointer]:
      - /url: http://127.0.0.1:8000
    - navigation [ref=f1e6]:
      - link "Beranda" [ref=f1e7] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa
      - link "Katalog Kuis" [ref=f1e11] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/kuis
      - link "Tugas" [ref=f1e15] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/tugas
      - link "Prestasi" [ref=f1e19] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/prestasi
      - link "Peringkat" [ref=f1e23] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/peringkat
      - link "Pengumuman" [ref=f1e26] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/pengumuman
      - link "Statistik" [ref=f1e30] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/statistik
      - link "Absensi" [ref=f1e32] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/absensi
      - link "Riwayat" [ref=f1e36] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/siswa/riwayat
    - generic [ref=f1e40]:
      - link "SE Siswa E2E 2026001 Siswa" [ref=f1e41] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/profil
        - generic [ref=f1e42]: SE
        - generic [ref=f1e43]:
          - generic [ref=f1e44]: Siswa E2E 2026001
          - generic [ref=f1e45]: Siswa
      - button "Keluar" [ref=f1e47] [cursor=pointer]
  - main [ref=f1e52]:
    - generic [ref=f1e53]:
      - heading "Halo, Siswa! 👋" [level=1] [ref=f1e54]
      - paragraph [ref=f1e55]: Ayo lanjutkan belajarmu hari ini.
    - generic [ref=f1e56]:
      - generic [ref=f1e57]:
        - generic [ref=f1e58]: 0%
        - generic [ref=f1e60]:
          - paragraph [ref=f1e61]: Progres Keseluruhan
          - paragraph [ref=f1e62]: 0 dari 0 kuis sudah kamu selesaikan.
      - generic [ref=f1e63]:
        - generic [ref=f1e64]:
          - paragraph [ref=f1e69]: "0"
          - paragraph [ref=f1e70]: Kuis Lulus
        - generic [ref=f1e71]:
          - paragraph [ref=f1e74]: –
          - paragraph [ref=f1e75]: Rata-rata Nilai
        - generic [ref=f1e76]:
          - paragraph [ref=f1e81]: "0"
          - paragraph [ref=f1e82]: Jam Belajar
        - generic [ref=f1e83]:
          - paragraph [ref=f1e87]: "0"
          - paragraph [ref=f1e88]: Hari Berturut-turut
    - generic [ref=f1e89]:
      - generic [ref=f1e90]:
        - heading "Kuis Terbaru" [level=2] [ref=f1e91]
        - link "Lihat Semua" [ref=f1e92] [cursor=pointer]:
          - /url: http://127.0.0.1:8000/siswa/kuis
      - generic [ref=f1e95]: Belum ada kuis untukmu. Tunggu guru menerbitkannya ya.
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import { ACCOUNTS, NIS, siswa } from '../fixtures/accounts';
  3  | import { ROUTES } from '../fixtures/routes';
  4  | import { login, expectForbidden } from '../helpers/auth';
  5  | 
  6  | // Alur 5 + deny-by-default / scope (PRD 2.3).
  7  | test.describe('Akses & IDOR', () => {
  8  |   test('ortu melihat anaknya, 403 untuk anak lain', async ({ page }) => {
  9  |     await login(page, ACCOUNTS.ortu2026001);
  10 |     await page.goto(ROUTES.student360(NIS.xRpl1(1)));
  11 |     await expect(page.getByTestId('rate-value')).toBeVisible();
  12 |     await expectForbidden(page, ROUTES.student360(NIS.xRpl1(2)));
  13 |   });
  14 | 
  15 |   test('siswa tidak dapat membuka siswa lain', async ({ page }) => {
  16 |     await login(page, siswa(NIS.xRpl1(1)));
  17 |     await expectForbidden(page, ROUTES.student360(NIS.xRpl1(2)));
  18 |   });
  19 | 
  20 |   test('guru hanya melihat siswa kelas yang diajar', async ({ page }) => {
  21 |     await login(page, ACCOUNTS.guruTkj);
  22 |     await expectForbidden(page, ROUTES.student360(NIS.xRpl1(1)));
  23 |   });
  24 | 
  25 |   test('siswa tidak dapat membuka halaman admin', async ({ page }) => {
  26 |     await login(page, siswa(NIS.xRpl1(1)));
  27 |     await expectForbidden(page, ROUTES.auditLog);
  28 |   });
  29 | 
  30 |   test('siswa dan ortu tidak melihat rata-rata kelas / peringkat', async ({ page }) => {
  31 |     await login(page, siswa(NIS.xRpl1(1)));
  32 |     await expect(page.getByTestId('class-average')).toHaveCount(0);
> 33 |     await expect(page.getByText(/peringkat|leaderboard/i)).toHaveCount(0);
     |                                                            ^ Error: expect(locator).toHaveCount(expected) failed
  34 |   });
  35 | 
  36 |   test('guru/wali melihat rata-rata kelas (kelas >= 5 siswa terisi)', async ({ page }) => {
  37 |     await login(page, ACCOUNTS.waliXRpl1);
  38 |     await page.goto(ROUTES.student360(NIS.xRpl1(1)));
  39 |     await expect(page.getByTestId('class-average')).toBeVisible();
  40 |   });
  41 | });
  42 | 
```