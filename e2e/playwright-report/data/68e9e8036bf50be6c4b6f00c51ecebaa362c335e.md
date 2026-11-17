# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 01-attendance-qr.spec.ts >> Absensi QR dinamis >> Layar QR hanya menampilkan jumlah hadir, tanpa nama siswa
- Location: tests\01-attendance-qr.spec.ts:25:3

# Error details

```
"beforeAll" hook timeout of 120000ms exceeded.
```

```
Error: locator.click: Target page, context or browser has been closed
Call log:
  - waiting for getByTestId('start-attendance-x-rpl-1')

```

```
TypeError: Cannot read properties of undefined (reading 'close')
```

# Page snapshot

```yaml
- generic [ref=f2e2]:
  - complementary [ref=f2e3]:
    - link "NeuroTrack" [ref=f2e4] [cursor=pointer]:
      - /url: http://127.0.0.1:8000
    - navigation [ref=f2e6]:
      - link "Beranda" [ref=f2e7] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru
      - link "Kuis Saya" [ref=f2e11] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/kuis
      - link "Tugas" [ref=f2e15] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/tugas
      - link "Pengumuman" [ref=f2e19] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/pengumuman
      - link "Penghargaan" [ref=f2e23] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/penghargaan
      - link "Peringkat Kelas" [ref=f2e27] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/peringkat
      - link "Absensi" [ref=f2e30] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/absensi
      - link "Penilaian Siswa" [ref=f2e34] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/siswa
    - generic [ref=f2e40]:
      - link "WX Wali X RPL 1 E2E Guru" [ref=f2e41] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/profil
        - generic [ref=f2e42]: WX
        - generic [ref=f2e43]:
          - generic [ref=f2e44]: Wali X RPL 1 E2E
          - generic [ref=f2e45]: Guru
      - button "Keluar" [ref=f2e47] [cursor=pointer]
  - main [ref=f2e52]:
    - generic [ref=f2e53]:
      - generic [ref=f2e54]:
        - heading "Halo, Wali X RPL 1 E2E" [level=1] [ref=f2e55]
        - paragraph [ref=f2e56]: Kelola kuis untuk mata pelajaran yang kamu ampu.
      - link "Buat Kuis" [ref=f2e57] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/guru/kuis/create
    - generic [ref=f2e59]:
      - generic [ref=f2e60]:
        - paragraph [ref=f2e65]: "0"
        - paragraph [ref=f2e66]: Kuis
      - generic [ref=f2e67]:
        - paragraph [ref=f2e72]: "0"
        - paragraph [ref=f2e73]: Diterbitkan
      - generic [ref=f2e74]:
        - paragraph [ref=f2e79]: "0"
        - paragraph [ref=f2e80]: Total Soal
      - generic [ref=f2e81]:
        - paragraph [ref=f2e88]: "0"
        - paragraph [ref=f2e89]: Pengerjaan Selesai
    - generic [ref=f2e90]:
      - generic [ref=f2e91]:
        - heading "Mapel yang Diampu" [level=2] [ref=f2e92]
        - generic [ref=f2e93]:
          - generic [ref=f2e95]:
            - paragraph [ref=f2e96]: Bahasa Inggris E2E
            - paragraph [ref=f2e97]: Umum
          - generic [ref=f2e99]:
            - paragraph [ref=f2e100]: DPK-RPL
            - paragraph [ref=f2e101]: Kejuruan
          - generic [ref=f2e103]:
            - paragraph [ref=f2e104]: Matematika E2E
            - paragraph [ref=f2e105]: Umum
          - generic [ref=f2e107]:
            - paragraph [ref=f2e108]: Sejarah
            - paragraph [ref=f2e109]: Umum
      - generic [ref=f2e110]:
        - heading "Pengerjaan Terbaru" [level=2] [ref=f2e111]
        - paragraph [ref=f2e113]: Belum ada siswa yang menyelesaikan kuismu.
```

# Test source

```ts
  1   | import { test, expect, BrowserContext, Page } from '@playwright/test';
  2   | import { ACCOUNTS, NIS, KELAS, INTERVAL, siswa } from '../fixtures/accounts';
  3   | import { login, switchUser } from '../helpers/auth';
  4   | import { startSession, scanUrl, scanAs, openMonitor } from '../helpers/attendance';
  5   | 
  6   | // Alur 1 PROMPTS Fase 9 + Kriteria Penerimaan PRD 13 (absensi QR). Berurutan: satu sesi harian.
  7   | test.describe.configure({ mode: 'serial' });
  8   | 
  9   | test.describe('Absensi QR dinamis', () => {
  10  |   let guruCtx: BrowserContext, hpCtx: BrowserContext;
  11  |   let qr: Page, monitor: Page, hp: Page;
  12  | 
  13  |   test.beforeAll(async ({ browser }) => {
  14  |     guruCtx = await browser.newContext();
  15  |     qr = await guruCtx.newPage();
  16  |     monitor = await guruCtx.newPage();
  17  |     await login(qr, ACCOUNTS.waliXRpl1);
  18  |     await startSession(qr, KELAS.xRpl1);
  19  |     await openMonitor(qr, monitor);
  20  |     hpCtx = await browser.newContext(); // "satu HP" dipakai bergantian beberapa siswa
  21  |     hp = await hpCtx.newPage();
  22  |   });
> 23  |   test.afterAll(async () => { await guruCtx.close(); await hpCtx.close(); });
      |                                                                  ^ TypeError: Cannot read properties of undefined (reading 'close')
  24  | 
  25  |   test('Layar QR hanya menampilkan jumlah hadir, tanpa nama siswa', async () => {
  26  |     const names = await monitor.getByTestId('student-name').allInnerTexts();
  27  |     expect(names.length).toBeGreaterThan(0);
  28  |     await expect(qr.getByTestId('present-count')).toHaveText(/^\s*\d+\s*\/\s*\d+\s*$/);
  29  |     const body = await qr.locator('body').innerText();
  30  |     for (const n of names) expect(body).not.toContain(n.trim());
  31  |   });
  32  | 
  33  |   test('QR berganti sesuai interval (20-30 dtk)', async () => {
  34  |     const first = await scanUrl(qr);
  35  |     const t0 = Date.now();
  36  |     await expect.poll(() => scanUrl(qr), { timeout: 32_000 }).not.toBe(first);
  37  |     expect((Date.now() - t0) / 1000).toBeLessThanOrEqual(30);
  38  |   });
  39  | 
  40  |   test('siswa terdaftar tercatat; pindai dobel ditolak', async () => {
  41  |     await login(hp, siswa(NIS.xRpl1(1)));
  42  |     const url = await scanUrl(qr);
  43  |     await expect(await scanAs(hp, url)).toContainText(/Absen tercatat/i);
  44  |     await expect(await scanAs(hp, await scanUrl(qr))).toContainText(/sudah|tercatat/i);
  45  |     await expect(monitor.getByTestId(`row-${NIS.xRpl1(1)}`).getByTestId('row-status')).toContainText(/Hadir/i);
  46  |   });
  47  | 
  48  |   test('siswa kelas lain ditolak', async ({ browser }) => {
  49  |     const ctx = await browser.newContext(); const p = await ctx.newPage();
  50  |     await login(p, siswa(NIS.xTkj1));
  51  |     await expect(await scanAs(p, await scanUrl(qr))).toContainText(/tidak terdaftar|bukan kelas/i);
  52  |     await ctx.close();
  53  |   });
  54  | 
  55  |   test('siswa yang belum login diarahkan ke halaman login', async ({ browser }) => {
  56  |     const ctx = await browser.newContext(); const p = await ctx.newPage();
  57  |     await p.goto(await scanUrl(qr));
  58  |     await expect(p).toHaveURL(/login/);
  59  |     await ctx.close();
  60  |   });
  61  | 
  62  |   test('jam HP diubah tidak berpengaruh; waktu pindai dari server', async ({ browser }) => {
  63  |     const ctx = await browser.newContext(); const p = await ctx.newPage();
  64  |     await p.clock.setFixedTime(new Date('2020-01-01T00:00:00Z'));
  65  |     await login(p, siswa(NIS.xRpl1(5)));
  66  |     await expect(await scanAs(p, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i);
  67  |     const iso = await monitor.getByTestId(`row-${NIS.xRpl1(5)}`).getByTestId('row-scanned-at').getAttribute('data-iso');
  68  |     expect(iso).toContain(String(new Date().getFullYear()));
  69  |     await ctx.close();
  70  |   });
  71  | 
  72  |   test('perangkat sama dipakai 2 akun -> keduanya "Perlu dicek", tidak ditolak', async () => {
  73  |     await switchUser(hp, siswa(NIS.xRpl1(2)));
  74  |     await expect(await scanAs(hp, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i);
  75  |     for (const n of [1, 2]) {
  76  |       await expect(monitor.getByTestId(`row-${NIS.xRpl1(n)}`).getByTestId('row-flag')).toContainText(/Perlu dicek/i, { timeout: 10_000 });
  77  |     }
  78  |   });
  79  | 
  80  |   test('token kedaluwarsa (> interval + 5 dtk) ditolak', async ({ browser }) => {
  81  |     const ctx = await browser.newContext(); const p = await ctx.newPage();
  82  |     await login(p, siswa(NIS.xRpl1(3)));
  83  |     const stale = await scanUrl(qr);
  84  |     await p.waitForTimeout((INTERVAL + 6) * 1000);
  85  |     await expect(await scanAs(p, stale)).toContainText(/kedaluwarsa/i);
  86  |     await expect(await scanAs(p, await scanUrl(qr))).toContainText(/Absen tercatat|Terlambat/i); // QR terbaru diterima
  87  |     await ctx.close();
  88  |   });
  89  | 
  90  |   test('Hadir manual wajib alasan dan tercatat', async () => {
  91  |     const row = monitor.getByTestId(`row-${NIS.xRpl1(4)}`);
  92  |     await row.getByTestId('btn-manual').click();
  93  |     await monitor.getByTestId('manual-submit').click();
  94  |     await expect(monitor.getByTestId('manual-error')).toBeVisible(); // alasan kosong ditolak
  95  |     await monitor.getByTestId('manual-reason').fill('HP rusak');
  96  |     await monitor.getByTestId('manual-submit').click();
  97  |     await expect(row.getByTestId('row-status')).toContainText(/Hadir/i);
  98  |     await expect(row.getByTestId('row-source')).toContainText(/manual/i);
  99  |   });
  100 | 
  101 |   test('tutup sesi, tandai sisanya Alpa, finalisasi', async () => {
  102 |     await monitor.getByTestId('btn-close-session').click();
  103 |     await monitor.getByTestId('btn-mark-rest-alpa').click();
  104 |     await monitor.getByTestId('btn-finalize').click();
  105 |     await monitor.getByTestId('confirm-finalize').click();
  106 |     await expect(monitor.getByTestId('session-status')).toHaveText(/FINAL|Final/);
  107 |     await expect(monitor.getByTestId(`row-${NIS.xRpl1(30)}`).getByTestId('row-status')).toContainText(/Alpa/i);
  108 |   });
  109 | 
  110 |   test('satu kelas hanya satu sesi per hari ("Buka kembali")', async () => {
  111 |     await qr.goto('/dashboard');
  112 |     await expect(qr.getByTestId(`start-attendance-${KELAS.xRpl1}`)).toHaveText(/Buka kembali/i);
  113 |     await expect(qr.getByTestId('sessions-today-x-rpl-1')).toHaveCount(1);
  114 |   });
  115 | });
  116 | 
```