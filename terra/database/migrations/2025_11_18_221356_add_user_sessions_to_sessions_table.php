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
        // Jika tabel sessions belum ada, buat struktur minimal
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->text('payload');
                $table->integer('last_activity')->index();
            });
        }

        // Tambahkan kolom tambahan hanya jika belum ada
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('sessions', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('last_activity')->index();
                }
                if (!Schema::hasColumn('sessions', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('sessions', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                if (Schema::hasColumn('sessions', 'user_id')) {
                    // Hapus index dulu jika ada, lalu kolom
                    $table->dropIndex(['user_id']);
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('sessions', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('sessions', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
            });
        }
    }
};
