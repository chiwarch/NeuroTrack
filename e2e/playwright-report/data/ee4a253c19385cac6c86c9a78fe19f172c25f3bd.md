# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 05-radar-hard-skill.spec.ts >> Radar Hard Skill >> sumbu kelas X dan XI jurusan sama berbeda; mapel yang tak diajarkan tidak jadi sumbu
- Location: tests\05-radar-hard-skill.spec.ts:7:3

# Error details

```
Error: expect(received).not.toEqual(expected) // deep equality

Expected: not []

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
> 18 |     expect(x).not.toEqual(xi);
     |                   ^ Error: expect(received).not.toEqual(expected) // deep equality
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
  45 |     await page.getByTestId('compare-toggle').first().click();
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