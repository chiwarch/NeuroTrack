import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS, KELAS } from '../fixtures/accounts';
import { ROUTES } from '../fixtures/routes';
import { login, switchUser, expectForbidden } from '../helpers/auth';

// Alur 3. Skala 1-4, nilai sumbu = rata-rata penilai x 25 (skala radar 25-100). Periode soft skill terbuka.
const DIMS = ['komunikasi', 'kerja-sama', 'kepemimpinan', 'etos-kerja', 'adaptabilitas'];
const SUBS = ['problem-solving', 'critical-thinking', 'kreativitas'];

test.describe.configure({ mode: 'serial' });
test.describe('Soft skill', () => {
  test('skor 1 dan 4 tidak dapat disimpan tanpa catatan bukti', async ({ page }) => {
    await login(page, ACCOUNTS.waliXRpl1);
    await page.goto(ROUTES.softskill(KELAS.xRpl1, NIS.xRpl1(2)));
    for (const score of ['1', '4']) {
      await page.getByTestId('soft-score-komunikasi').selectOption(score);
      await page.getByTestId('soft-evidence-komunikasi').fill('');
      await page.getByTestId('soft-save').click();
      await expect(page.getByTestId('soft-error-komunikasi')).toBeVisible();
    }
    await page.getByTestId('soft-evidence-komunikasi').fill('Memimpin diskusi kelompok dengan baik');
    await page.getByTestId('soft-save').click();
    await expect(page.getByTestId('soft-error-komunikasi')).toHaveCount(0);
  });

  test('guru tanpa hubungan dengan siswa tidak dapat menilai (403)', async ({ page }) => {
    await login(page, ACCOUNTS.guruTkj);
    await expectForbidden(page, ROUTES.softskill(KELAS.xRpl1, NIS.xRpl1(1)));
  });

  test('wali + guru mapel menilai; radar = rata-rata penilai x 25; sub-indikator dirata-rata', async ({ page }) => {
    const nis = NIS.xRpl1(1);
    // wali: semua 3; sub Berpikir 2,3,4 (rata-rata 3). Skor 4 butuh bukti.
    await login(page, ACCOUNTS.waliXRpl1);
    await page.goto(ROUTES.softskill(KELAS.xRpl1, nis));
    for (const d of DIMS) await page.getByTestId(`soft-score-${d}`).selectOption('3');
    const wali = { 'problem-solving': '2', 'critical-thinking': '3', kreativitas: '4' } as Record<string, string>;
    for (const s of SUBS) await page.getByTestId(`soft-score-berpikir-${s}`).selectOption(wali[s]);
    await page.getByTestId('soft-evidence-berpikir-kreativitas').fill('Membuat solusi inovatif pada projek');
    await page.getByTestId('soft-save').click();
    await expect(page.getByTestId('soft-saved')).toBeVisible();

    // guru mapel: semua 3
    await switchUser(page, ACCOUNTS.guruRpl);
    await page.goto(ROUTES.softskill(KELAS.xRpl1, nis));
    for (const d of DIMS) await page.getByTestId(`soft-score-${d}`).selectOption('3');
    for (const s of SUBS) await page.getByTestId(`soft-score-berpikir-${s}`).selectOption('3');
    await page.getByTestId('soft-save').click();
    await expect(page.getByTestId('soft-saved')).toBeVisible();

    // radar soft skill: 6 sumbu, semua (3+3)/2 x 25 = 75
    await page.goto(ROUTES.student360(nis));
    const axes = page.locator('[data-testid^="softradar-axis-value-"]');
    await expect(axes).toHaveCount(6);
    for (const v of await axes.allInnerTexts()) expect(parseFloat(v.replace(',', '.'))).toBe(75);
  });
});
