<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tabel cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            // Hapus foreign key sementara agar index bisa di-drop
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            
            // Sekarang aturan unik lama aman untuk dihapus
            $table->dropUnique(['user_id', 'product_id']);
            
            // Tambahkan relasi ke varian
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('cascade');
            
            // Pasang kembali foreign key yang tadi dilepas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Buat aturan unik baru (dengan 3 kombinasi kolom)
            $table->unique(['user_id', 'product_id', 'product_variant_id'], 'cart_items_user_product_variant_unique');
        });

        // 2. Update tabel order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('set null');
            
            // Simpan nama varian sebagai snapshot
            $table->string('variant_name_snapshot')->nullable()->after('product_name_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Copot semua foreign key terlebih dahulu
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);

            // Hapus kolom dan index varian
            $table->dropUnique('cart_items_user_product_variant_unique');
            $table->dropColumn('product_variant_id');

            // Kembalikan foreign key dan unique index ke kondisi awal
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['user_id', 'product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_variant_id', 'variant_name_snapshot']);
        });
    }
};