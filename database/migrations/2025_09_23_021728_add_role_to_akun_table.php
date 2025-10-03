<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToAkunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('akun', 'role')) {
            Schema::table('akun', function (Blueprint $table) {
                $table->string('role')->default('karyawan');
                // default jadi karyawan, nanti admin bisa manual ubah
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('akun', 'role')) {
            Schema::table('akun', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
}
