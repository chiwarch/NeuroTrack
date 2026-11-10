import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS, siswa } from '../fixtures/accounts';
import { ROUTES } from '../fixtures/routes';
import { login, expectForbidden } from '../helpers/auth';

// Alur 5 + deny-by-default / scope (PRD 2.3).
test.describe('Akses & IDOR', () => {
  test('ortu melihat anaknya, 403 untuk anak lain', async ({ page }) => {
    await login(page, ACCOUNTS.ortu2026001);
    await page.goto(ROUTES.student360(NIS.xRpl1(1)));
    await expect(page.getByTestId('rate-value')).toBeVisible();
    await expectForbidden(page, ROUTES.student360(NIS.xRpl1(2)));
  });

  test('siswa tidak dapat membuka siswa lain', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    await expectForbidden(page, ROUTES.student360(NIS.xRpl1(2)));
  });

  test('guru hanya melihat siswa kelas yang diajar', async ({ page }) => {
    await login(page, ACCOUNTS.guruTkj);
    await expectForbidden(page, ROUTES.student360(NIS.xRpl1(1)));
  });

  test('siswa tidak dapat membuka halaman admin', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    await expectForbidden(page, ROUTES.auditLog);
  });

  test('siswa dan ortu tidak melihat rata-rata kelas / peringkat', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    await expect(page.getByTestId('class-average')).toHaveCount(0);
    await expect(page.getByText(/peringkat|leaderboard/i)).toHaveCount(0);
  });

  test('guru/wali melihat rata-rata kelas (kelas >= 5 siswa terisi)', async ({ page }) => {
    await login(page, ACCOUNTS.waliXRpl1);
    await page.goto(ROUTES.student360(NIS.xRpl1(1)));
    await expect(page.getByTestId('class-average')).toBeVisible();
  });
});
