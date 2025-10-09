<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSubdivisiToKaryawanTable extends Migration
{
    public function up()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_subdivisi')->nullable()->after('id_divisi');

            // Relasi ke tabel subdepartement
            $table->foreign('id_subdivisi')
                ->references('id_subdivisi')
                ->on('subdepartement')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['id_subdivisi']);
            $table->dropColumn('id_subdivisi');
        });
    }
}
