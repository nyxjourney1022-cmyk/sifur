<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1-5
            $table->text('komentar')->nullable();
            $table->timestamps();

            // Satu user hanya bisa review satu produk sekali
            $table->unique(['user_id', 'barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
