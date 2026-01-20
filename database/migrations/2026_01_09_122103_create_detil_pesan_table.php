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
        Schema::create('detil_pesan', function (Blueprint $table) {
            $table->integer('id_pesan', 10);
            $table->string('id_produk', 5);
            $table->integer('jumlah')->nullable();
            $table->decimal('harga', 10, 0)->nullable();
            $table->timestamps();

            // 1. Menentukan Primary Key Gabungan (Composite Key)
            $table->primary(['id_pesan', 'id_produk']);

            // 2. Membuat Relasi ke tabel pesan
            $table->foreign('id_pesan')
                ->references('id_pesan')
                ->on('pesan')
                ->onDelete('cascade');

            // 3. Membuat Relasi ke tabel produk
            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detil_pesan');
    }
};
