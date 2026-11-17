# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 05-radar-hard-skill.spec.ts >> Radar Hard Skill >> toggle "Bandingkan dengan semester lalu" memunculkan poligon kedua
- Location: tests\05-radar-hard-skill.spec.ts:42:3

# Error details

```
Test timeout of 120000ms exceeded.
```

```
Error: locator.click: Test timeout of 120000ms exceeded.
Call log:
  - waiting for getByTestId('compare-toggle').first()

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
  2  | import { NIS, siswa } from '../fixtures/accounts';
  3  | import { login } from '../helpers/auth';
  4  | 
  5  | // Alur 4 + PRD 3.6.2 / 3.6.4.
  6  | test.describe('Radar Hard Skill', () => {
  7  |   test('sumbu kelas X dan XI jurusan sama berbeda; mapel yang tak diajarkan tidak jadi sumbu', async ({ browser }) => {
  8  |     const get = async (nis: string) => {
  9  |       const ctx = await browser.newContext(); const p = await ctx.newPage();
  10 |       await login(p, siswa(nis));
  11 |       const keys = await p.locator('[data-testid^="radar-axis-"]:not([data-testid*="value"]):not([data-testid*="label"])')
  12 |         .evaluateAll(els => els.map(e => e.getAttribute('data-testid')!));
  13 |       await ctx.close();
  14 |       return keys;
  15 |     };
  16 |     const x = await get(NIS.xRpl1(1));
  17 |     const xi = await get(NIS.xiRpl);
  18 |     expect(x).not.toEqual(xi);
  19 |     expect(x).toContain('radar-axis-sejarah');
  20 |     expect(xi).not.toContain('radar-axis-sejarah');
  21 |   });
  22 | 
  23 |   test('sumbu tanpa nilai tampil "belum dinilai", bukan 0', async ({ page }) => {
  24 |     await login(page, siswa(NIS.xRpl1(3)));
  25 |     const unrated = page.locator('[data-testid^="radar-axis-value-"]', { hasText: /belum dinilai/i });
  26 |     await expect(unrated.first()).toBeVisible();
  27 |     await expect(page.locator('[data-testid^="radar-axis-value-"]', { hasText: /^\s*0\s*$/ })).toHaveCount(0);
  28 |   });
  29 | 
  30 |   test('sumbu < 3 -> diagram batang', async ({ page }) => {
  31 |     await login(page, siswa(NIS.fewAxes));
  32 |     await expect(page.getByTestId('hard-bar-chart')).toBeVisible();
  33 |     await expect(page.getByTestId('hard-radar-chart')).toHaveCount(0);
  34 |   });
  35 | 
  36 |   test('tampilan tabel alternatif tersedia', async ({ page }) => {
  37 |     await login(page, siswa(NIS.xRpl1(1)));
  38 |     await page.getByTestId('radar-table-toggle').first().click();
  39 |     await expect(page.getByRole('table').first()).toBeVisible();
  40 |   });
  41 | 
  42 |   test('toggle "Bandingkan dengan semester lalu" memunculkan poligon kedua', async ({ page }) => {
  43 |     await login(page, siswa(NIS.xRpl1(1)));
  44 |     await expect(page.getByTestId('radar-overlay')).toHaveCount(0);
> 45 |     await page.getByTestId('compare-toggle').first().click();
     |                                                      ^ Error: locator.click: Test timeout of 120000ms exceeded.
  46 |     await expect(page.getByTestId('radar-overlay').first()).toBeVisible();
  47 |   });
  48 | 
  49 |   test('label sumbu tidak saling menimpa di layar 375 px @mobile', async ({ page }) => {
  50 |     await login(page, siswa(NIS.xRpl1(1)));
  51 |     const labels = page.locator('[data-testid^="radar-axis-label-"]');
  52 |     const n = await labels.count();
  53 |     expect(n).toBeGreaterThanOrEqual(3);
  54 |     const boxes = [];
  55 |     for (let i = 0; i < n; i++) boxes.push((await labels.nth(i).boundingBox())!);
  56 |     for (let i = 0; i < n; i++) for (let j = i + 1; j < n; j++) {
  57 |       const a = boxes[i], b = boxes[j];
  58 |       const overlap = a.x < b.x + b.width && b.x < a.x + a.width && a.y < b.y + b.height && b.y < a.y + a.height;
  59 |       expect(overlap, `label ${i} dan ${j} menimpa`).toBeFalsy();
  60 |     }
  61 |   });
  62 | });
  63 | 
```