import { test, expect } from '@playwright/test';
import { NIS, siswa } from '../fixtures/accounts';
import { login } from '../helpers/auth';

// Alur 4 + PRD 3.6.2 / 3.6.4.
test.describe('Radar Hard Skill', () => {
  test('sumbu kelas X dan XI jurusan sama berbeda; mapel yang tak diajarkan tidak jadi sumbu', async ({ browser }) => {
    const get = async (nis: string) => {
      const ctx = await browser.newContext(); const p = await ctx.newPage();
      await login(p, siswa(nis));
      const keys = await p.locator('[data-testid^="radar-axis-"]:not([data-testid*="value"]):not([data-testid*="label"])')
        .evaluateAll(els => els.map(e => e.getAttribute('data-testid')!));
      await ctx.close();
      return keys;
    };
    const x = await get(NIS.xRpl1(1));
    const xi = await get(NIS.xiRpl);
    expect(x).not.toEqual(xi);
    expect(x).toContain('radar-axis-sejarah');
    expect(xi).not.toContain('radar-axis-sejarah');
  });

  test('sumbu tanpa nilai tampil "belum dinilai", bukan 0', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(3)));
    const unrated = page.locator('[data-testid^="radar-axis-value-"]', { hasText: /belum dinilai/i });
    await expect(unrated.first()).toBeVisible();
    await expect(page.locator('[data-testid^="radar-axis-value-"]', { hasText: /^\s*0\s*$/ })).toHaveCount(0);
  });

  test('sumbu < 3 -> diagram batang', async ({ page }) => {
    await login(page, siswa(NIS.fewAxes));
    await expect(page.getByTestId('hard-bar-chart')).toBeVisible();
    await expect(page.getByTestId('hard-radar-chart')).toHaveCount(0);
  });

  test('tampilan tabel alternatif tersedia', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    await page.getByTestId('radar-table-toggle').first().click();
    await expect(page.getByRole('table').first()).toBeVisible();
  });

  test('toggle "Bandingkan dengan semester lalu" memunculkan poligon kedua', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    await expect(page.getByTestId('radar-overlay')).toHaveCount(0);
    await page.getByTestId('compare-toggle').first().click();
    await expect(page.getByTestId('radar-overlay').first()).toBeVisible();
  });

  test('label sumbu tidak saling menimpa di layar 375 px @mobile', async ({ page }) => {
    await login(page, siswa(NIS.xRpl1(1)));
    const labels = page.locator('[data-testid^="radar-axis-label-"]');
    const n = await labels.count();
    expect(n).toBeGreaterThanOrEqual(3);
    const boxes = [];
    for (let i = 0; i < n; i++) boxes.push((await labels.nth(i).boundingBox())!);
    for (let i = 0; i < n; i++) for (let j = i + 1; j < n; j++) {
      const a = boxes[i], b = boxes[j];
      const overlap = a.x < b.x + b.width && b.x < a.x + a.width && a.y < b.y + b.height && b.y < a.y + a.height;
      expect(overlap, `label ${i} dan ${j} menimpa`).toBeFalsy();
    }
  });
});
