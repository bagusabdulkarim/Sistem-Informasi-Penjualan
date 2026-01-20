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
            // id_kuitansi string(5) PRIMARY KEY
            $table->string('id_kuitansi')->primary();

            // id_faktur string(5) - Foreign Key ke tabel faktur
            $table->string('id_faktur');

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
