# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 07-kenaikan-kelas.spec.ts >> kenaikan kelas dikonfirmasi lalu di-undo
- Location: tests\07-kenaikan-kelas.spec.ts:7:1

# Error details

```
Test timeout of 120000ms exceeded.
```

```
Error: locator.innerText: Test timeout of 120000ms exceeded.
Call log:
  - waiting for getByTestId('student-class')

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
  2  | import { ACCOUNTS, NIS } from '../fixtures/accounts';
  3  | import { ROUTES } from '../fixtures/routes';
  4  | import { login } from '../helpers/auth';
  5  | 
  6  | // Alur 6: wizard kenaikan kelas + undo (PRD 3.3).
  7  | test('kenaikan kelas dikonfirmasi lalu di-undo', async ({ page }) => {
  8  |   await login(page, ACCOUNTS.admin);
  9  |   await page.goto(ROUTES.student360(NIS.promotable));
> 10 |   const before = await page.getByTestId('student-class').innerText();
     |                                                          ^ Error: locator.innerText: Test timeout of 120000ms exceeded.
  11 | 
  12 |   await page.goto(ROUTES.promotion);
  13 |   await page.getByTestId('promo-preview').click();
  14 |   await expect(page.getByTestId('promo-preview-table')).toBeVisible();
  15 |   await page.getByTestId('promo-confirm').click();
  16 |   await page.getByTestId('promo-confirm-yes').click();
  17 | 
  18 |   await page.goto(ROUTES.student360(NIS.promotable));
  19 |   await expect(page.getByTestId('student-class')).not.toHaveText(before);
  20 | 
  21 |   await page.goto(ROUTES.promotion);
  22 |   await page.getByTestId('promo-undo').click();
  23 |   await page.getByTestId('promo-undo-yes').click();
  24 |   await page.goto(ROUTES.student360(NIS.promotable));
  25 |   await expect(page.getByTestId('student-class')).toHaveText(before);
  26 | });
  27 | 
```