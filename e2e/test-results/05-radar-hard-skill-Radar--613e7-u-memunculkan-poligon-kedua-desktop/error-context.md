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
        - generic [ref=f1e58]: 100,0%
        - generic [ref=f1e60]:
          - paragraph [ref=f1e61]: Rate Kehadiran
          - paragraph [ref=f1e62]: Rate dihitung dari hadir + terlambat dibanding semua hari tercatat. Dispensasi tidak dihitung.
      - generic [ref=f1e63]:
        - generic [ref=f1e64]:
          - paragraph [ref=f1e65]: "1"
          - paragraph [ref=f1e66]: Hadir
        - generic [ref=f1e67]:
          - paragraph [ref=f1e68]: "0"
          - paragraph [ref=f1e69]: Terlambat
        - generic [ref=f1e70]:
          - paragraph [ref=f1e71]: "0"
          - paragraph [ref=f1e72]: Sakit
        - generic [ref=f1e73]:
          - paragraph [ref=f1e74]: "0"
          - paragraph [ref=f1e75]: Alfa
    - generic [ref=f1e76]:
      - generic [ref=f1e77]:
        - generic [ref=f1e78]: 0%
        - generic [ref=f1e80]:
          - paragraph [ref=f1e81]: Progres Keseluruhan
          - paragraph [ref=f1e82]: 0 dari 0 kuis sudah kamu selesaikan.
      - generic [ref=f1e83]:
        - generic [ref=f1e84]:
          - paragraph [ref=f1e89]: "0"
          - paragraph [ref=f1e90]: Kuis Lulus
        - generic [ref=f1e91]:
          - paragraph [ref=f1e94]: –
          - paragraph [ref=f1e95]: Rata-rata Nilai
        - generic [ref=f1e96]:
          - paragraph [ref=f1e101]: "0"
          - paragraph [ref=f1e102]: Jam Belajar
        - generic [ref=f1e103]:
          - paragraph [ref=f1e107]: "0"
          - paragraph [ref=f1e108]: Hari Berturut-turut
    - generic [ref=f1e109]:
      - generic [ref=f1e110]:
        - heading "Kuis Terbaru" [level=2] [ref=f1e111]
        - link "Lihat Semua" [ref=f1e112] [cursor=pointer]:
          - /url: http://127.0.0.1:8000/siswa/kuis
      - generic [ref=f1e115]: Belum ada kuis untukmu. Tunggu guru menerbitkannya ya.
    - generic [ref=f1e117]:
      - generic [ref=f1e118]:
        - heading "Hard Skill" [level=2] [ref=f1e119]
        - paragraph [ref=f1e120]: Dari nilai kuis tiap mata pelajaran (0–100).
        - img "Radar chart" [ref=f1e122]:
          - generic [ref=f1e132]: BahasaInggris E2E
          - generic [ref=f1e133]: MatematikaE2E
          - generic [ref=f1e134]: Sejarah
          - generic [ref=f1e135]: DPK-RPL
        - generic [ref=f1e137]:
          - generic [ref=f1e138]:
            - generic [ref=f1e139]:
              - paragraph [ref=f1e140]: Bahasa Inggris E2E
              - paragraph [ref=f1e141]: belum dinilai
            - paragraph [ref=f1e143]: Belum ada kuis atau tugas
          - generic [ref=f1e144]:
            - generic [ref=f1e145]:
              - paragraph [ref=f1e146]: Matematika E2E
              - paragraph [ref=f1e147]: belum dinilai
            - paragraph [ref=f1e149]: Belum ada kuis atau tugas
          - generic [ref=f1e150]:
            - generic [ref=f1e151]:
              - paragraph [ref=f1e152]: Sejarah
              - paragraph [ref=f1e153]: belum dinilai
            - paragraph [ref=f1e155]: Belum ada kuis atau tugas
          - generic [ref=f1e156]:
            - generic [ref=f1e157]:
              - paragraph [ref=f1e158]: DPK-RPL
              - paragraph [ref=f1e159]: belum dinilai
            - paragraph [ref=f1e161]: Belum ada kuis atau tugas
      - generic [ref=f1e162]:
        - heading "Soft Skill" [level=2] [ref=f1e163]
        - paragraph [ref=f1e164]: Penilaian guru, skala 1–5 (rata-rata semua guru).
        - img "Radar chart" [ref=f1e166]:
          - generic [ref=f1e183]: Komunikasi
          - generic [ref=f1e184]: Kerja Sama
          - generic [ref=f1e185]: Kepemimpinan
          - generic [ref=f1e186]: Berpikir &PemecahanMasalah
          - generic [ref=f1e187]: Etos Kerja &TanggungJawab
          - generic [ref=f1e188]: Adaptabilitas
        - generic [ref=f1e190]:
          - generic [ref=f1e191]:
            - generic [ref=f1e192]:
              - paragraph [ref=f1e193]: Komunikasi
              - paragraph [ref=f1e194]: 3,0
            - paragraph [ref=f1e197]: Dinilai 1 guru
          - generic [ref=f1e198]:
            - generic [ref=f1e199]:
              - paragraph [ref=f1e200]: Kerja Sama
              - paragraph [ref=f1e201]: 3,0
            - paragraph [ref=f1e204]: Dinilai 1 guru
          - generic [ref=f1e205]:
            - generic [ref=f1e206]:
              - paragraph [ref=f1e207]: Kepemimpinan
              - paragraph [ref=f1e208]: 3,0
            - paragraph [ref=f1e211]: Dinilai 1 guru
          - generic [ref=f1e212]:
            - generic [ref=f1e213]:
              - paragraph [ref=f1e214]: Berpikir & Pemecahan Masalah
              - paragraph [ref=f1e215]: 3,0
            - paragraph [ref=f1e218]: Dinilai 1 guru
          - generic [ref=f1e219]:
            - generic [ref=f1e220]:
              - paragraph [ref=f1e221]: Etos Kerja & Tanggung Jawab
              - paragraph [ref=f1e222]: 3,0
            - paragraph [ref=f1e225]: Dinilai 1 guru
          - generic [ref=f1e226]:
            - generic [ref=f1e227]:
              - paragraph [ref=f1e228]: Adaptabilitas
              - paragraph [ref=f1e229]: 3,0
            - paragraph [ref=f1e232]: Dinilai 1 guru
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