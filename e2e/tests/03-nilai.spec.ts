import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS, KELAS, MAPEL } from '../fixtures/accounts';
import { ROUTES } from '../fixtures/routes';
import { login, switchUser, expectForbidden } from '../helpers/auth';

// Alur 2: guru input nilai -> kunci -> revisi -> persetujuan -> audit. Prasyarat: periode terbuka, nilai NIS 2026001 kosong.
test.describe.configure({ mode: 'serial' });

test.describe('Nilai mapel', () => {
  const nis = NIS.xRpl1(1);
  test('guru tanpa penugasan tidak dapat membuka grid nilai (403)', async ({ page }) => {
    await login(page, ACCOUNTS.guruTkj);
    await expectForbidden(page, ROUTES.grades(KELAS.xRpl1, MAPEL.dpkRpl));
  });

  test('guru input nilai; di luar 0-100 ditolak', async ({ page }) => {
    await login(page, ACCOUNTS.guruRpl);
    await page.goto(ROUTES.grades(KELAS.xRpl1, MAPEL.dpkRpl));
    await page.getByTestId(`grade-${nis}`).fill('101');
    await page.getByTestId('grade-save').click();
    await expect(page.getByTestId(`grade-error-${nis}`)).toBeVisible();
    await page.getByTestId(`grade-${nis}`).fill('80');
    await page.getByTestId('grade-save').click();
    await expect(page.getByTestId(`final-${nis}`)).toHaveText(/80/);
  });

  test('admin mengunci periode; guru tidak dapat mengedit lagi', async ({ page }) => {
    await login(page, ACCOUNTS.admin);
    await page.goto(ROUTES.periods);
    await page.getByTestId('lock-period').click();
    await page.getByTestId('confirm-lock').click();
    await switchUser(page, ACCOUNTS.guruRpl);
    await page.goto(ROUTES.grades(KELAS.xRpl1, MAPEL.dpkRpl));
    await expect(page.getByTestId(`grade-${nis}`)).toBeDisabled();
  });

  test('revisi wajib alasan, disetujui Kepsek, tercatat lama -> baru di audit', async ({ page }) => {
    await login(page, ACCOUNTS.guruRpl);
    await page.goto(ROUTES.grades(KELAS.xRpl1, MAPEL.dpkRpl));
    await page.getByTestId(`request-revision-${nis}`).click();
    await page.getByTestId('revision-new-value').fill('90');
    await page.getByTestId('revision-submit').click();
    await expect(page.getByTestId('revision-error')).toBeVisible(); // alasan wajib
    await page.getByTestId('revision-reason').fill('Salah input nilai praktik');
    await page.getByTestId('revision-submit').click();

    await switchUser(page, ACCOUNTS.kepsek);
    await page.goto(ROUTES.revisions);
    await page.getByTestId('approve-revision').first().click();

    await page.goto(ROUTES.auditLog);
    await expect(page.getByTestId('audit-row').filter({ hasText: /80.*90/ }).first()).toBeVisible();
    await switchUser(page, ACCOUNTS.guruRpl);
    await page.goto(ROUTES.grades(KELAS.xRpl1, MAPEL.dpkRpl));
    await expect(page.getByTestId(`final-${nis}`)).toHaveText(/90/);
  });
});
