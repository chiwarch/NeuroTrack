import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS, siswa } from '../fixtures/accounts';
import { login } from '../helpers/auth';

// PRD 3.5.4: Rate = (E - T) / E x 100. Dispensasi, PKL, terlambat tidak mengurangi.
test.describe('Rate kehadiran', () => {
  test('contoh PRD: 60 efektif, 2 dispensasi, S2 I1 A3 -> 89,7%', async ({ page }) => {
    await login(page, siswa(NIS.rateExample));
    await expect(page.getByTestId('rate-value')).toHaveText(/89,7\s*%/);
    await expect(page.getByTestId('rate-formula')).toBeVisible(); // rumus transparan
  });

  test('siswa PKL berbulan-bulan tidak menurunkan rate', async ({ page }) => {
    await login(page, siswa(NIS.pkl));
    await expect(page.getByTestId('rate-value')).toHaveText(/100(,0)?\s*%/);
  });

  test('terlambat tidak mengurangi rate, tetapi dicatat terpisah', async ({ page }) => {
    await login(page, siswa(NIS.late));
    await expect(page.getByTestId('rate-value')).toHaveText(/100(,0)?\s*%/);
    await expect(page.getByTestId('late-count')).toHaveText(/5/);
  });

  test('dashboard guru menandai hari yang belum difinalisasi', async ({ page }) => {
    await login(page, ACCOUNTS.guruRpl);
    await expect(page.getByTestId('unfinalized-warning')).toBeVisible();
  });
});
