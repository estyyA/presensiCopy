<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubdepartementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subdepartement', function (Blueprint $table) {
            $table->increments('id_subdivisi');
            $table->integer('id_divisi'); // ⚠️ jangan pakai unsigned
            $table->string('nama_subdivisi', 100);

            // Foreign key (tipe harus sama dengan tabel departement)
            $table->foreign('id_divisi')
                  ->references('id_divisi')
                  ->on('departement')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subdepartement');
    }
}
