<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSakitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sakit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('NIK', 20);
            $table->date('tgl_pengajuan')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan')->nullable();
            $table->string('surat_dokter', 255)->nullable()->comment('Path file surat dokter');
            $table->enum('status_pengajuan', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->datetime('tanggal_disetujui')->nullable();
            $table->string('disetujui_oleh', 50)->nullable();

            // Relasi ke tabel karyawan
            $table->foreign('NIK')
                  ->references('NIK')
                  ->on('karyawan')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('sakit');
    }
}
