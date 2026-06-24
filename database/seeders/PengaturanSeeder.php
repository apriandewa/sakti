<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Pengaturan::create([
            'id' => Str::uuid(),
            'judul' => 'PPID Kabupaten Indragiri Hulu',
            'subjudul' => 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu',
            'deskripsi' => 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Kabupaten Indragiri Hulu.',
            'alamat' => 'Jl. Lintas Timur, Pematang Reba, Rengat Barat, Kabupaten Indragiri Hulu, Riau',
            'telepon' => '(0769) 341012',
            'email' => 'ppid@inhukab.go.id',
            'peta' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.281898744005!2d102.42898827409559!3d-0.49089069950431326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2be28974a682ff%3A0xc34dfcc907bc025d!2sDinas%20Komunikasi%20dan%20Informatika%20Kabupaten%20Indragiri%20Hulu!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid',
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
            'twiter' => 'https://twitter.com',
            'tiktok' => 'https://tiktok.com',
            'youtube' => 'https://youtube.com',
            'call_center' => '112',
        ]);
    }
}
