<?php

namespace Database\Seeders;

use App\Models\StatusPegawai;
use App\Models\User;
use Illuminate\Database\Seeder;

class StatusPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rootUser = User::where('email', 'root@mail.com')->first();
        $userId = $rootUser ? $rootUser->id : null;

        $statuses = [
            ['nama' => 'PNS', 'desc' => 'Pegawai Negeri Sipil', 'keterangan' => 'Status Pegawai Negeri Sipil'],
            ['nama' => 'CPNS', 'desc' => 'Calon Pegawai Negeri Sipil', 'keterangan' => 'Status Calon Pegawai Negeri Sipil'],
            ['nama' => 'PPPK', 'desc' => 'Pegawai Pemerintah dengan Perjanjian Kerja', 'keterangan' => 'Status PPPK'],
            ['nama' => 'PPPK-PW', 'desc' => 'Pegawai Pemerintah dengan Perjanjian Kerja Paruh Waktu', 'keterangan' => 'Status PPPK Paruh Waktu'],
            ['nama' => 'KONTRAK', 'desc' => 'Tenaga Kerja Kontrak / Honorer', 'keterangan' => 'Status Tenaga Kontrak'],
            ['nama' => 'MAGANG', 'desc' => 'Peserta Magang / Prakerin', 'keterangan' => 'Status Magang'],
        ];

        foreach ($statuses as $status) {
            StatusPegawai::updateOrCreate(
                ['nama' => $status['nama']],
                [
                    'desc' => $status['desc'],
                    'keterangan' => $status['keterangan'],
                    'status' => 'aktif',
                    'user_id' => $userId
                ]
            );
        }
    }
}
