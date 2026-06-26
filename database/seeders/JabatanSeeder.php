<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
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

        // --- ROOT LEVEL (Jenis Jabatan) ---
        $struktural = Jabatan::updateOrCreate(
            ['nama' => 'Pejabat Struktural', 'parent_id' => null],
            ['desc' => 'Kelompok Jabatan Struktural', 'keterangan' => 'Struktural', 'status' => 'aktif', 'user_id' => $userId]
        );

        $fungsional = Jabatan::updateOrCreate(
            ['nama' => 'Pejabat Fungsional', 'parent_id' => null],
            ['desc' => 'Kelompok Jabatan Fungsional', 'keterangan' => 'Fungsional', 'status' => 'aktif', 'user_id' => $userId]
        );

        $pelaksana = Jabatan::updateOrCreate(
            ['nama' => 'Staf Pelaksana', 'parent_id' => null],
            ['desc' => 'Kelompok Staf Pelaksana / Pelaksana', 'keterangan' => 'Pelaksana', 'status' => 'aktif', 'user_id' => $userId]
        );

        $pppk = Jabatan::updateOrCreate(
            ['nama' => 'PPPK', 'parent_id' => null],
            ['desc' => 'Kelompok Pegawai Pemerintah dengan Perjanjian Kerja', 'keterangan' => 'PPPK', 'status' => 'aktif', 'user_id' => $userId]
        );


        // --- CHILD LEVEL: Pejabat Struktural ---
        $strukturalChildren = [
            'Kepala Dinas',
            'Sekretaris',
            'Kabid IKP',
            'Kabid Egov',
            'Kabid Statistik',
            'Kasubbag Umum',
            'Kasubbag Keuangan',
        ];
        foreach ($strukturalChildren as $nama) {
            Jabatan::updateOrCreate(
                ['nama' => $nama, 'parent_id' => $struktural->id],
                ['desc' => $nama, 'keterangan' => 'Jabatan Struktural', 'status' => 'aktif', 'user_id' => $userId]
            );
        }

        // --- CHILD LEVEL: Pejabat Fungsional ---
        $fungsionalChildren = [
            'Pranata Komputer Ahli Muda',
            'Sandiman Ahli Muda',
        ];
        foreach ($fungsionalChildren as $nama) {
            Jabatan::updateOrCreate(
                ['nama' => $nama, 'parent_id' => $fungsional->id],
                ['desc' => $nama, 'keterangan' => 'Jabatan Fungsional', 'status' => 'aktif', 'user_id' => $userId]
            );
        }

        // --- CHILD LEVEL: Staf Pelaksana ---
        $pelaksanaChildren = [
            'Penelaah Teknis Kebijakan',
        ];
        foreach ($pelaksanaChildren as $nama) {
            Jabatan::updateOrCreate(
                ['nama' => $nama, 'parent_id' => $pelaksana->id],
                ['desc' => $nama, 'keterangan' => 'Staf Pelaksana', 'status' => 'aktif', 'user_id' => $userId]
            );
        }

        // --- CHILD LEVEL: PPPK ---
        $pppkChildren = [
            'Penata Layanan Operasional',
            'Pranata Komputer Ahli Pertama',
            'Arsiparis Ahli Pertama',
        ];
        foreach ($pppkChildren as $nama) {
            Jabatan::updateOrCreate(
                ['nama' => $nama, 'parent_id' => $pppk->id],
                ['desc' => $nama, 'keterangan' => 'Jabatan PPPK', 'status' => 'aktif', 'user_id' => $userId]
            );
        }
    }
}
