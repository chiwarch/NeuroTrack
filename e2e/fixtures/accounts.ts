export const PASSWORD = process.env.E2E_PASSWORD ?? 'Password123!';
const acc = (id: string) => ({ id, password: PASSWORD });

export const ACCOUNTS = {
  admin: acc('admin@neurotrack.test'),
  kepsek: acc('kepsek@neurotrack.test'),
  guruRpl: acc('guru.rpl@neurotrack.test'), // mengajar X RPL 1 (mapel kejuruan RPL)
  guruTkj: acc('guru.tkj@neurotrack.test'), // TIDAK mengajar X RPL 1
  waliXRpl1: acc('wali.xrpl1@neurotrack.test'),
  ortu2026001: acc('ortu.2026001@neurotrack.test'), // hanya tertaut ke NIS 2026001
};

/** Akun siswa: username = NIS (PRD 3.1). */
export const siswa = (nis: string) => acc(nis);

export const NIS = {
  xRpl1: (n: number) => String(2026000 + n), // 2026001..2026035 (35 siswa, X RPL 1)
  xTkj1: '2026101',                          // siswa kelas lain
  xiRpl: '2025001',                          // XI RPL (struktur mapel berbeda dari X)
  rateExample: '2026900',                    // 60 efektif, 2 dispensasi, S2 I1 A3 -> 89,7%
  pkl: '2025901',                            // PKL 3 bulan, rate 100%
  late: '2026902',                           // 5x terlambat, tanpa alpa, rate 100%
  fewAxes: '2026910',                        // jurusan dengan struktur mapel < 3 sumbu
  promotable: '2025950',                     // XI RPL 1, akan dinaikkan ke XII
};

export const KELAS = { xRpl1: 'x-rpl-1', xTkj1: 'x-tkj-1' };
export const MAPEL = { dpkRpl: 'DPK-RPL' };
export const INTERVAL = Number(process.env.QR_INTERVAL_SECONDS ?? 20);
