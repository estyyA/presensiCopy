<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_sales', function (Blueprint $table) {
            $table->bigIncrements('id');

            // relasi ke karyawan (NIK varchar(20))
            $table->string('NIK', 20)->collation('utf8mb4_general_ci');
            $table->foreign('NIK')->references('NIK')->on('karyawan')->onDelete('cascade');

            // relasi ke departement (id_divisi int)
            $table->integer('id_divisi');
            $table->foreign('id_divisi')->references('id_divisi')->on('departement')->onDelete('cascade');

            // data utama
            $table->date('tanggal_sales');
            $table->time('jam_sales');
            $table->string('lokasi_sales', 255);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_sales');
    }
}
