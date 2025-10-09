<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Department extends Seeder
{
    public function run()
    {
        DB::table('departement')->truncate(); // kosongkan tabel dulu

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
