<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel products
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Nama varian, contoh: "250 Gram", "500 Gram", "1 Kg"
            $table->string('variant_name');
            
            // SKU, Harga, Stok, dan Berat sekarang ada di level varian
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('weight')->default(0); // dalam satuan gram
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};