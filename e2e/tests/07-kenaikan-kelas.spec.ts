import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS } from '../fixtures/accounts';
import { ROUTES } from '../fixtures/routes';
import { login } from '../helpers/auth';

// Alur 6: wizard kenaikan kelas + undo (PRD 3.3).
test('kenaikan kelas dikonfirmasi lalu di-undo', async ({ page }) => {
  await login(page, ACCOUNTS.admin);
  await page.goto(ROUTES.student360(NIS.promotable));
  const before = await page.getByTestId('student-class').innerText();

  await page.goto(ROUTES.promotion);
  await page.getByTestId('promo-preview').click();
  await expect(page.getByTestId('promo-preview-table')).toBeVisible();
  await page.getByTestId('promo-confirm').click();
  await page.getByTestId('promo-confirm-yes').click();

  await page.goto(ROUTES.student360(NIS.promotable));
  await expect(page.getByTestId('student-class')).not.toHaveText(before);

  await page.goto(ROUTES.promotion);
  await page.getByTestId('promo-undo').click();
  await page.getByTestId('promo-undo-yes').click();
  await page.goto(ROUTES.student360(NIS.promotable));
  await expect(page.getByTestId('student-class')).toHaveText(before);
});
