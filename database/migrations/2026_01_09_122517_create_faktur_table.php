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
        Schema::create('faktur', function (Blueprint $table) {
            // id_faktur string(5) PRIMARY KEY
            $table->string('id_faktur')->primary();

            // id_pesan int(5) - Foreign Key ke tabel pesan
            $table->integer('id_pesan');

            // tgl_faktur date
            $table->date('tgl_faktur');

            $table->timestamps();

            // Membuat Relasi ke tabel pesan
            $table->foreign('id_pesan')
                ->references('id_pesan')
                ->on('pesan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur');
    }
};
