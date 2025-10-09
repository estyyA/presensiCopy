<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubdepartementSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('subdepartement')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('subdepartement')->insert([
            // Kabag. Hub Kelembagaan, SDM & Umum (id_divisi = 1)
            ['id_divisi' => 1, 'nama_subdivisi' => 'Staf SDM & Umum'],
            ['id_divisi' => 1, 'nama_subdivisi' => 'Staf Pengadaan'],
            ['id_divisi' => 1, 'nama_subdivisi' => 'Staf Hukum & Kelembagaan'],
            ['id_divisi' => 1, 'nama_subdivisi' => 'Staf Pemasaran & ATR'],

            // Kabag Tanaman (id_divisi = 2)
            ['id_divisi' => 2, 'nama_subdivisi' => 'SKK'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'SKW'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'SKT TLD'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'SKW TLD'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'Kepala Tebang Angkut & Mekanisasi'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'Staf Velanas'],
            ['id_divisi' => 2, 'nama_subdivisi' => 'Staf Tebang & Angkut'],

            // Kabag Instalasi PG-PS (id_divisi = 3)
            ['id_divisi' => 3, 'nama_subdivisi' => 'Staf Gilingan'],
            ['id_divisi' => 3, 'nama_subdivisi' => 'Staf Ketel'],
            ['id_divisi' => 3, 'nama_subdivisi' => 'Staf Permurnian & ATR'],

            // Kabag Akuntansi & Keuangan (id_divisi = 4)
            ['id_divisi' => 4, 'nama_subdivisi' => 'Staf Keuangan & Pajak'],
            ['id_divisi' => 4, 'nama_subdivisi' => 'Staf Akuntansi'],
            ['id_divisi' => 4, 'nama_subdivisi' => 'Staf IT & Timbangan'],

            // Kabag QA (Quality Assurance) (id_divisi = 5)
            ['id_divisi' => 5, 'nama_subdivisi' => 'Staf QA On Farm'],
            ['id_divisi' => 5, 'nama_subdivisi' => 'Staf QA Off Farm'],

            // Kabag Pabrikasi PG-PS (id_divisi = 6)
            ['id_divisi' => 6, 'nama_subdivisi' => 'Staf Teknik Pabrik Spiritus'],
            ['id_divisi' => 6, 'nama_subdivisi' => 'Staf Listrik & Instrumen'],
            ['id_divisi' => 6, 'nama_subdivisi' => 'Staf Besali'],
            ['id_divisi' => 6, 'nama_subdivisi' => 'Staf Pabrikasi Pabrik Spiritus'],
        ]);
    }
}
