<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePasswordFromKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('karyawan', function (Blueprint $table) {
        $table->dropColumn('password');
    });
}

public function down()
{
    Schema::table('karyawan', function (Blueprint $table) {
        $table->string('password')->nullable();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
}
