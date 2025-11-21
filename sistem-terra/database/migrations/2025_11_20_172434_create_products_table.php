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
    Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa penjualnya
            $table->string('name');
            $table->text('description');
            $table->integer('price');
            $table->string('image')->nullable(); // Foto produk
            $table->string('whatsapp_number');   // No WA Penjual
            $table->timestamps();
    });

    // KITA TAMBAH KOLOM ROLE KE TABEL USER DISINI SEKALIAN BIAR CEPET
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('petani'); // Default jadi petani
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
