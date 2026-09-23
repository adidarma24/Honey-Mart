<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode unik kupon (cth: PROMO2026)
            $table->enum('type', ['fixed', 'percent']); // Potongan harga tetap atau persenan
            $table->decimal('value', 12, 2); // Nominal atau Persentase
            $table->integer('usage_limit')->nullable(); 
            $table->integer('used_count')->default(0); 
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};