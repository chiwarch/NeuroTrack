# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 06-akses-idor.spec.ts >> Akses & IDOR >> guru/wali melihat rata-rata kelas (kelas >= 5 siswa terisi)
- Location: tests\06-akses-idor.spec.ts:36:3

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: getByTestId('class-average')
Expected: visible
Timeout: 8000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" getByTestId('class-average') with timeout 8000ms
  - waiting for getByTestId('class-average')

```

```yaml
- main:
  - heading "404" [level=1]
  - text: Not Found
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
  33 |     await expect(page.getByText(/peringkat|leaderboard/i)).toHaveCount(0);
  34 |   });
  35 | 
  36 |   test('guru/wali melihat rata-rata kelas (kelas >= 5 siswa terisi)', async ({ page }) => {
  37 |     await login(page, ACCOUNTS.waliXRpl1);
  38 |     await page.goto(ROUTES.student360(NIS.xRpl1(1)));
> 39 |     await expect(page.getByTestId('class-average')).toBeVisible();
     |                                                     ^ Error: expect(locator).toBeVisible() failed
  40 |   });
  41 | });
  42 | 
```