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
    Schema::table('akun', function (Blueprint $table) {
        $table->string('role')->default('karyawan');
        // default jadi karyawan, nanti admin bisa manual ubah
    });
}

public function down()
{
    Schema::table('akun', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}

}
