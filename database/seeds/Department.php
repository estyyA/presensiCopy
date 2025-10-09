<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Department extends Seeder
{
    public function run()
    {
        // Hapus sub_divisi dulu agar tidak bentrok foreign key
        DB::table('sub_divisi')->delete();

        // Sekarang aman hapus departement
        DB::table('departement')->delete();
        DB::statement('ALTER TABLE departement AUTO_INCREMENT = 1;');

        DB::table('departement')->insert([
            ['nama_divisi' => 'Kabag. Hub Kelembagaan, SDM, Umum'],
            ['nama_divisi' => 'Kabag. Tanaman'],
            ['nama_divisi' => 'Kabag. Instalasi'],
            ['nama_divisi' => 'Kabag. Akuntansi & Keuangan'],
            ['nama_divisi' => 'Kabag. QA'],
            ['nama_divisi' => 'Kabag. Pabrikasi'],
        ]);
    }
}
