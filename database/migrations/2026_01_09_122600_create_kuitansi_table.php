<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kuitansi', function (Blueprint $table) {
            // id_kuitansi int(5) PRIMARY KEY
            $table->integer('id_kuitansi')->primary();

            // id_faktur int(5) - Foreign Key ke tabel faktur
            $table->integer('id_faktur');

            // tgl_kuitansi date
            $table->date('tgl_kuitansi');



            $table->timestamps();

            // Membuat Relasi ke tabel faktur
            $table->foreign('id_faktur')
                ->references('id_faktur')
                ->on('faktur')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuitansi');
    }
};
