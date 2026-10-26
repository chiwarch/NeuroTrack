<?php

return [
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa email yang valid.',
    'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa angka bulat.',
    'array' => ':attribute tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'unique' => ':attribute sudah dipakai.',
    'between' => [
        'numeric' => ':attribute harus di antara :min dan :max.',
        'string' => ':attribute harus di antara :min dan :max karakter.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'attributes' => [
        'name' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi', 'role' => 'Role', 'nis' => 'NIS',
        'major_id' => 'Jurusan', 'grade_level' => 'Tingkat', 'code' => 'Kode', 'type' => 'Jenis', 'icon' => 'Ikon',
        'subject_id' => 'Mapel', 'title' => 'Judul', 'description' => 'Deskripsi', 'duration_minutes' => 'Durasi',
        'passing_score' => 'Nilai lulus', 'body' => 'Teks soal', 'points' => 'Poin', 'teachers' => 'Guru',
    ],
];
