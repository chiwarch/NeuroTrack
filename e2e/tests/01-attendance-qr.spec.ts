import { test, expect, BrowserContext, Page } from '@playwright/test';
import { ACCOUNTS, NIS, KELAS, INTERVAL, siswa } from '../fixtures/accounts';
import { login, switchUser } from '../helpers/auth';
import { startSession, scanUrl, scanAs, openMonitor } from '../helpers/attendance';

// Alur 1 PROMPTS Fase 9 + Kriteria Penerimaan PRD 13 (absensi QR). Berurutan: satu sesi harian.
test.describe.configure({ mode: 'serial' });

test.describe('Absensi QR dinamis', () => {
  let guruCtx: BrowserContext, hpCtx: BrowserContext;
  let qr: Page, monitor: Page, hp: Page;

  test.beforeAll(async ({ browser }) => {
    guruCtx = await browser.newContext();
    qr = await guruCtx.newPage();
    monitor = await guruCtx.newPage();
    await login(qr, ACCOUNTS.waliXRpl1);
    await startSession(qr, KELAS.xRpl1);
    await openMonitor(qr, monitor);
    hpCtx = await browser.newContext(); // "satu HP" dipakai bergantian beberapa siswa
    hp = await hpCtx.newPage();
  });
  test.afterAll(async () => { await guruCtx.close(); await hpCtx.close(); });

  test('Layar QR hanya menampilkan jumlah hadir, tanpa nama siswa', async () => {
    const names = await monitor.getByTestId('student-name').allInnerTexts();
    expect(names.length).toBeGreaterThan(0);
    await expect(qr.getByTestId('present-count')).toHaveText(/^\s*\d+\s*\/\s*\d+\s*$/);
    const body = await qr.locator('body').innerText();
    for (const n of names) expect(body).not.toContain(n.trim());
  });

  test('QR berganti sesuai interval (20-30 dtk)', async () => {
    const first = await scanUrl(qr);
    const t0 = Date.now();
    await expect.poll(() => scanUrl(qr), { timeout: 32_000 }).not.toBe(first);
    expect((Date.now() - t0) / 1000).toBeLessThanOrEqual(30);
  });

  test('siswa terdaftar tercatat; pindai dobel ditolak', async () => {
    await login(hp, siswa(NIS.xRpl1(1)));
    const url = await scanUrl(qr);
    await expect(await scanAs(hp, url)).toContainText(/Absen tercatat/i);
    await expect(await scanAs(hp, await scanUrl(qr))).toContainText(/sudah|tercatat/i);
    await expect(monitor.getByTestId(`row-${NIS.xRpl1(1)}`).getByTestId('row-status')).toContainText(/Hadir/i);
  });

  test('siswa kelas lain ditolak', async ({ browser }) => {
    const ctx = await browser.newContext(); const p = await ctx.newPage();
    await login(p, siswa(NIS.xTkj1));
    await expect(await scanAs(p, await scanUrl(qr))).toContainText(/tidak terdaftar|bukan kelas/i);
    await ctx.close();
  });

  test('siswa yang belum login diarahkan ke halaman login', async ({ browser }) => {
    const ctx = await browser.newContext(); const p = await ctx.newPage();
    await p.goto(await scanUrl(qr));
    await expect(p).toHaveURL(/login/);
    await ctx.close();
  });

  test('jam HP diubah tidak berpengaruh; waktu pindai dari server', async ({ browser }) => {
    const ctx = await browser.newContext(); const p = await ctx.newPage();
    await p.clock.setFixedTime(new Date('2020-01-01T00:00:00Z'));
    await login(p, siswa(NIS.xRpl1(5)));
    await expect(await scanAs(p, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i);
    const iso = await monitor.getByTestId(`row-${NIS.xRpl1(5)}`).getByTestId('row-scanned-at').getAttribute('data-iso');
    expect(iso).toContain(String(new Date().getFullYear()));
    await ctx.close();
  });

  test('perangkat sama dipakai 2 akun -> keduanya "Perlu dicek", tidak ditolak', async () => {
    await switchUser(hp, siswa(NIS.xRpl1(2)));
    await expect(await scanAs(hp, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i);
    for (const n of [1, 2]) {
      await expect(monitor.getByTestId(`row-${NIS.xRpl1(n)}`).getByTestId('row-flag')).toContainText(/Perlu dicek/i, { timeout: 10_000 });
    }
  });

  test('token kedaluwarsa (> interval + 5 dtk) ditolak', async ({ browser }) => {
    const ctx = await browser.newContext(); const p = await ctx.newPage();
    await login(p, siswa(NIS.xRpl1(3)));
    const stale = await scanUrl(qr);
    await p.waitForTimeout((INTERVAL + 6) * 1000);
    await expect(await scanAs(p, stale)).toContainText(/kedaluwarsa/i);
    await expect(await scanAs(p, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i); // QR terbaru diterima
    await ctx.close();
  });

  test('Hadir manual wajib alasan dan tercatat', async () => {
    const row = monitor.getByTestId(`row-${NIS.xRpl1(4)}`);
    await row.getByTestId('btn-manual').click();
    await monitor.getByTestId('manual-submit').click();
    await expect(monitor.getByTestId('manual-error')).toBeVisible(); // alasan kosong ditolak
    await monitor.getByTestId('manual-reason').fill('HP rusak');
    await monitor.getByTestId('manual-submit').click();
    await expect(row.getByTestId('row-status')).toContainText(/Hadir/i);
    await expect(row.getByTestId('row-source')).toContainText(/manual/i);
  });

  test('tutup sesi, tandai sisanya Alpa, finalisasi', async () => {
    await monitor.getByTestId('btn-close-session').click();
    await monitor.getByTestId('btn-mark-rest-alpa').click();
    await monitor.getByTestId('btn-finalize').click();
    await monitor.getByTestId('confirm-finalize').click();
    await expect(monitor.getByTestId('session-status')).toHaveText(/FINAL|Final/);
    await expect(monitor.getByTestId(`row-${NIS.xRpl1(30)}`).getByTestId('row-status')).toContainText(/Alpa/i);
  });

  test('satu kelas hanya satu sesi per hari ("Buka kembali")', async () => {
    await qr.goto('/dashboard');
    await expect(qr.getByTestId(`start-attendance-${KELAS.xRpl1}`)).toHaveText(/Buka kembali/i);
    await expect(qr.getByTestId('sessions-today-x-rpl-1')).toHaveCount(1);
  });
});
