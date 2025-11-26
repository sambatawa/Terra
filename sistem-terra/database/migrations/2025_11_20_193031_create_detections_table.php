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
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->string('dominan_disease')->nullable();
            $table->integer('confidence'); 
            $table->decimal('dominan_confidence_avg', 5, 4)->nullable();
            $table->json('jumlah_disease_terdeteksi')->nullable(); 
            $table->json('sensor_rata_rata')->nullable(); 
            $table->string('status')->nullable(); 
            $table->string('image_snapshot')->nullable(); 
            $table->json('info')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
