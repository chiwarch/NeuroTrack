import { test, expect } from '@playwright/test';
import { ACCOUNTS, NIS, KELAS, siswa } from '../fixtures/accounts';
import { login } from '../helpers/auth';
import { startSession, scanUrl } from '../helpers/attendance';

// PRD 8: pindai QR < 2 dtk (P95) saat ±35 siswa bersamaan. Jalankan: pnpm test:load (DB direset tiap run oleh global-setup, jadi sesi harian X RPL 1 masih kosong).
test('35 siswa memindai bersamaan, P95 < 2 dtk @load', async ({ browser }) => {
  const guruCtx = await browser.newContext(); const qr = await guruCtx.newPage();
  await login(qr, ACCOUNTS.waliXRpl1);
  await startSession(qr, KELAS.xRpl1);

  const students = await Promise.all(Array.from({ length: 35 }, async (_, i) => {
    const ctx = await browser.newContext(); const p = await ctx.newPage();
    await login(p, siswa(NIS.xRpl1(i + 1)));
    return { ctx, p };
  }));
  const url = await scanUrl(qr);
  const times = await Promise.all(students.map(async ({ p }) => {
    const t0 = Date.now();
    await p.goto(url);
    await expect(p.getByTestId('scan-result')).toContainText(/tercatat|Terlambat|sudah/i);
    return Date.now() - t0;
  }));
  times.sort((a, b) => a - b);
  const p95 = times[Math.ceil(times.length * 0.95) - 1];
  console.log(`P95 = ${p95} ms`);
  expect(p95).toBeLessThan(2000);
  await Promise.all(students.map(s => s.ctx.close())); await guruCtx.close();
});
