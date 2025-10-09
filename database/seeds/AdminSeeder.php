<?php



use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Pastikan dulu ada karyawan admin
        DB::table('karyawan')->updateOrInsert(
            ['NIK' => '0001'],
            [
                'nama_lengkap' => 'Super Admin',
                'email'        => 'adminSDM@gmail.com',
                'role'         => 'admin',
                'status'       => 'Aktif',
                'no_hp'        => '081234567890',
                'id_divisi'    => 1,
                'id_jabatan'   => 1,
            ]
        );

        // Buat akun login admin
        DB::table('akun')->updateOrInsert(
            ['username' => 'admin'],
            [
                'NIK'      => '0001',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}

