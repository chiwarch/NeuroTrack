# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 04-softskill.spec.ts >> Soft skill >> skor 1 dan 4 tidak dapat disimpan tanpa catatan bukti
- Location: tests\04-softskill.spec.ts:12:3

# Error details

```
Test timeout of 120000ms exceeded.
```

```
Error: locator.selectOption: Test timeout of 120000ms exceeded.
Call log:
  - waiting for getByTestId('soft-score-komunikasi')

```

# Page snapshot

```yaml
- main [ref=f2e2]:
  - generic [ref=f2e4]:
    - heading "404" [level=1] [ref=f2e5]
    - generic [ref=f2e6]: Not Found
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import { ACCOUNTS, NIS, KELAS } from '../fixtures/accounts';
  3  | import { ROUTES } from '../fixtures/routes';
  4  | import { login, switchUser, expectForbidden } from '../helpers/auth';
  5  | 
  6  | // Alur 3. Skala 1-4, nilai sumbu = rata-rata penilai x 25 (skala radar 25-100). Periode soft skill terbuka.
  7  | const DIMS = ['komunikasi', 'kerja-sama', 'kepemimpinan', 'etos-kerja', 'adaptabilitas'];
  8  | const SUBS = ['problem-solving', 'critical-thinking', 'kreativitas'];
  9  | 
  10 | test.describe.configure({ mode: 'serial' });
  11 | test.describe('Soft skill', () => {
  12 |   test('skor 1 dan 4 tidak dapat disimpan tanpa catatan bukti', async ({ page }) => {
  13 |     await login(page, ACCOUNTS.waliXRpl1);
  14 |     await page.goto(ROUTES.softskill(KELAS.xRpl1, NIS.xRpl1(2)));
  15 |     for (const score of ['1', '4']) {
> 16 |       await page.getByTestId('soft-score-komunikasi').selectOption(score);
     |                                                       ^ Error: locator.selectOption: Test timeout of 120000ms exceeded.
  17 |       await page.getByTestId('soft-evidence-komunikasi').fill('');
  18 |       await page.getByTestId('soft-save').click();
  19 |       await expect(page.getByTestId('soft-error-komunikasi')).toBeVisible();
  20 |     }
  21 |     await page.getByTestId('soft-evidence-komunikasi').fill('Memimpin diskusi kelompok dengan baik');
  22 |     await page.getByTestId('soft-save').click();
  23 |     await expect(page.getByTestId('soft-error-komunikasi')).toHaveCount(0);
  24 |   });
  25 | 
  26 |   test('guru tanpa hubungan dengan siswa tidak dapat menilai (403)', async ({ page }) => {
  27 |     await login(page, ACCOUNTS.guruTkj);
  28 |     await expectForbidden(page, ROUTES.softskill(KELAS.xRpl1, NIS.xRpl1(1)));
  29 |   });
  30 | 
  31 |   test('wali + guru mapel menilai; radar = rata-rata penilai x 25; sub-indikator dirata-rata', async ({ page }) => {
  32 |     const nis = NIS.xRpl1(1);
  33 |     // wali: semua 3; sub Berpikir 2,3,4 (rata-rata 3). Skor 4 butuh bukti.
  34 |     await login(page, ACCOUNTS.waliXRpl1);
  35 |     await page.goto(ROUTES.softskill(KELAS.xRpl1, nis));
  36 |     for (const d of DIMS) await page.getByTestId(`soft-score-${d}`).selectOption('3');
  37 |     const wali = { 'problem-solving': '2', 'critical-thinking': '3', kreativitas: '4' } as Record<string, string>;
  38 |     for (const s of SUBS) await page.getByTestId(`soft-score-berpikir-${s}`).selectOption(wali[s]);
  39 |     await page.getByTestId('soft-evidence-berpikir-kreativitas').fill('Membuat solusi inovatif pada projek');
  40 |     await page.getByTestId('soft-save').click();
  41 |     await expect(page.getByTestId('soft-saved')).toBeVisible();
  42 | 
  43 |     // guru mapel: semua 3
  44 |     await switchUser(page, ACCOUNTS.guruRpl);
  45 |     await page.goto(ROUTES.softskill(KELAS.xRpl1, nis));
  46 |     for (const d of DIMS) await page.getByTestId(`soft-score-${d}`).selectOption('3');
  47 |     for (const s of SUBS) await page.getByTestId(`soft-score-berpikir-${s}`).selectOption('3');
  48 |     await page.getByTestId('soft-save').click();
  49 |     await expect(page.getByTestId('soft-saved')).toBeVisible();
  50 | 
  51 |     // radar soft skill: 6 sumbu, semua (3+3)/2 x 25 = 75
  52 |     await page.goto(ROUTES.student360(nis));
  53 |     const axes = page.locator('[data-testid^="softradar-axis-value-"]');
  54 |     await expect(axes).toHaveCount(6);
  55 |     for (const v of await axes.allInnerTexts()) expect(parseFloat(v.replace(',', '.'))).toBe(75);
  56 |   });
  57 | });
  58 | 
```