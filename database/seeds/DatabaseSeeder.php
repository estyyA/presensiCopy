<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Jalankan seeder admin
        $this->call(AdminSeeder::class);

{
    $this->call([
        AdminSeeder::class,
        Department::class,
        SubdepartementSeeder::class,
    ]);
}

    }
}
