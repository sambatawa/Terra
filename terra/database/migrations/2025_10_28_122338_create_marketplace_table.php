<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->string('id_kategori', 10)->primary();
            $table->string('nama_kategori');
            $table->string('keyword', 5)->nullable();
            $table->timestamps();
        });
        Schema::create('produk', function (Blueprint $table) {
            $table->string('id_produk', 10)->primary();
            $table->string('id_user', 10);
            $table->string('id_kategori', 10);
            $table->string('nama_produk');
            $table->text('deskripsi');
            $table->decimal('harga', 12, 2);
            $table->integer('stok');
            $table->string('gambar')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_produk')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
        Schema::create('cart', function (Blueprint $table) {
            $table->string('id_cart', 10)->primary();
            $table->string('id_user', 10);
            $table->string('id_produk', 10);
            $table->integer('jumlah')->default(1);
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
        Schema::create('chatMarketplace', function (Blueprint $table) {
            $table->string('id_chat', 10)->primary();
            $table->string('id_pengirim', 10);
            $table->string('id_penerima', 10);
            $table->timestamps();
            $table->foreign('id_pengirim')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_penerima')->references('id_user')->on('users')->onDelete('cascade');      
        });
        Schema::create('message', function (Blueprint $table) {
            $table->string('id_message', 10)->primary();
            $table->string('id_chat', 10);
            $table->string('id_pengirim', 10);
            $table->string('id_penerima', 10);
            $table->text('pesan');
            $table->boolean('dibaca')->default(false);
            $table->timestamps(); 
            $table->foreign('id_chat')->references('id_chat')->on('chatMarketplace')->onDelete('cascade');
            $table->foreign('id_pengirim')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_penerima')->references('id_user')->on('users')->onDelete('cascade'); 
        });
        Schema::create('penilaian_produk', function (Blueprint $table) {
            $table->string('id_penilaian', 10)->primary();
            $table->string('id_produk', 10);
            $table->string('id_user', 10);
            $table->tinyInteger('rating')->unsigned();
            $table->text('ulasan')->nullable();
            $table->timestamps();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_produk');
        Schema::dropIfExists('message');
        Schema::dropIfExists('chatMarketplace');
        Schema::dropIfExists('cart');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori_produk');
    }
};
