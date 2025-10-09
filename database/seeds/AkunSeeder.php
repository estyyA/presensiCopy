<?php



use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['NIK' => 'EMP001', 'username' => 'budi', 'password' => Hash::make('123456')],
            ['NIK' => 'EMP002', 'username' => 'sari', 'password' => Hash::make('123456')],
            ['NIK' => 'EMP003', 'username' => 'andi', 'password' => Hash::make('123456')],
            ['NIK' => 'EMP004', 'username' => 'rina', 'password' => Hash::make('123456')],
            ['NIK' => 'EMP005', 'username' => 'agus', 'password' => Hash::make('123456')],
        ];

        foreach ($data as $akun) {
            DB::table('akun')->updateOrInsert(
                ['username' => $akun['username']], // cek dulu berdasarkan username
                $akun
            );
        }
    }
}
