<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use App\Models\User;
use Illuminate\Database\Seeder;

class PangkatSeeder extends Seeder
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

        $pangkats = [
            // Golongan I (Juru)
            ['nama' => 'Juru Muda (I/a)', 'desc' => 'Golongan I/a', 'keterangan' => 'Pangkat PNS Golongan I/a'],
            ['nama' => 'Juru Muda Tingkat I (I/b)', 'desc' => 'Golongan I/b', 'keterangan' => 'Pangkat PNS Golongan I/b'],
            ['nama' => 'Juru (I/c)', 'desc' => 'Golongan I/c', 'keterangan' => 'Pangkat PNS Golongan I/c'],
            ['nama' => 'Juru Tingkat I (I/d)', 'desc' => 'Golongan I/d', 'keterangan' => 'Pangkat PNS Golongan I/d'],

            // Golongan II (Pengatur)
            ['nama' => 'Pengatur Muda (II/a)', 'desc' => 'Golongan II/a', 'keterangan' => 'Pangkat PNS Golongan II/a'],
            ['nama' => 'Pengatur Muda Tingkat I (II/b)', 'desc' => 'Golongan II/b', 'keterangan' => 'Pangkat PNS Golongan II/b'],
            ['nama' => 'Pengatur (II/c)', 'desc' => 'Golongan II/c', 'keterangan' => 'Pangkat PNS Golongan II/c'],
            ['nama' => 'Pengatur Tingkat I (II/d)', 'desc' => 'Golongan II/d', 'keterangan' => 'Pangkat PNS Golongan II/d'],

            // Golongan III (Penata)
            ['nama' => 'Penata Muda (III/a)', 'desc' => 'Golongan III/a', 'keterangan' => 'Pangkat PNS Golongan III/a'],
            ['nama' => 'Penata Muda Tingkat I (III/b)', 'desc' => 'Golongan III/b', 'keterangan' => 'Pangkat PNS Golongan III/b'],
            ['nama' => 'Penata (III/c)', 'desc' => 'Golongan III/c', 'keterangan' => 'Pangkat PNS Golongan III/c'],
            ['nama' => 'Penata Tingkat I (III/d)', 'desc' => 'Golongan III/d', 'keterangan' => 'Pangkat PNS Golongan III/d'],

            // Golongan IV (Pembina)
            ['nama' => 'Pembina (IV/a)', 'desc' => 'Golongan IV/a', 'keterangan' => 'Pangkat PNS Golongan IV/a'],
            ['nama' => 'Pembina Tingkat I (IV/b)', 'desc' => 'Golongan IV/b', 'keterangan' => 'Pangkat PNS Golongan IV/b'],
            ['nama' => 'Pembina Utama Muda (IV/c)', 'desc' => 'Golongan IV/c', 'keterangan' => 'Pangkat PNS Golongan IV/c'],
            ['nama' => 'Pembina Utama Madya (IV/d)', 'desc' => 'Golongan IV/d', 'keterangan' => 'Pangkat PNS Golongan IV/d'],
            ['nama' => 'Pembina Utama (IV/e)', 'desc' => 'Golongan IV/e', 'keterangan' => 'Pangkat PNS Golongan IV/e'],
        ];

        foreach ($pangkats as $pangkat) {
            Pangkat::updateOrCreate(
                ['nama' => $pangkat['nama']],
                [
                    'desc' => $pangkat['desc'],
                    'keterangan' => $pangkat['keterangan'],
                    'status' => 'aktif',
                    'user_id' => $userId
                ]
            );
        }
    }
}
