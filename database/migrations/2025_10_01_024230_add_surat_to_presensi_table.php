<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuratToPresensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('presensi', function (Blueprint $table) {
        $table->string('surat')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('presensi', function (Blueprint $table) {
        $table->dropColumn('surat');
    });
}

}
