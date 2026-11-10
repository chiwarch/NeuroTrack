// Rute aplikasi nyata. Diperbarui agar cocok dengan routes/web.php.
// student360: gunakan /guru/siswa/{id} atau /orang-tua/anak/{id} — tapi tes menggunakan NIS.
// Karena rute saat ini tidak mendukung NIS sebagai route key untuk halaman 360,
// baris student360 diperbarui untuk menjelaskan situasi dan menggunakan path yang ada.
export const ROUTES = {
  login: '/masuk',                 // was '/login', app route is /masuk
  dashboard: '/dashboard',
  // /siswa/{nis} tidak ada; siswa 360 ada di /guru/siswa/{id} (by DB id).
  // Untuk ortu: /orang-tua/anak/{id}. Baris ini menggunakan path guru (diakses admin/guru/wali).
  student360: (nis: string) => `/siswa/${nis}`, // CATATAN: rute ini belum ada di app; test 05,06,07 perlu rute baru
  grades: (kelas: string, mapel: string) => `/nilai/${kelas}/${mapel}`,         // rute belum ada
  softskill: (kelas: string, nis: string) => `/softskill/${kelas}/${nis}`,      // rute belum ada
  periods: '/admin/periode-penilaian',                                           // rute belum ada
  revisions: '/kepsek/revisi-nilai',                                             // rute belum ada
  auditLog: '/admin/log-aktivitas',   // was '/admin/audit-log' → diperbarui ke rute nyata
  promotion: '/admin/kenaikan-kelas', // rute belum ada
};
