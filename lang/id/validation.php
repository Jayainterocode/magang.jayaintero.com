<?php

/*
|--------------------------------------------------------------------------
| Validation Messages (Bahasa Indonesia)
|--------------------------------------------------------------------------
|
| Covers the rules this application uses. Any rule missing here falls back
| to Laravel's English message through the fallback locale.
|
*/

return [
    'accepted' => ':Attribute wajib disetujui.',
    'after_or_equal' => ':Attribute harus sama dengan atau setelah :date.',
    'array' => ':Attribute harus berupa daftar.',
    'between' => [
        'numeric' => ':Attribute harus antara :min dan :max.',
        'string' => ':Attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => ':Attribute harus bernilai ya atau tidak.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'enum' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa berkas.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa angka bulat.',
    'max' => [
        'array' => ':Attribute maksimal berisi :max item.',
        'file' => ':Attribute maksimal :max kilobyte.',
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa berkas: :values.',
    'min' => [
        'array' => 'Pilih minimal :min :attribute.',
        'file' => ':Attribute minimal :min kilobyte.',
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'uploaded' => ':Attribute gagal diunggah.',

    'attributes' => [
        'email' => 'email',
        'password' => 'password',
    ],
];
