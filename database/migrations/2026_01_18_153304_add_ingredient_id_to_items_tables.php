<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة العمود لجدول المخزن
        Schema::table('pantry_items', function (Blueprint $table) {
            // نتأكد الأول إن العمود مش موجود عشان ميديناش ايرور لو عملنا رن مرتين
            if (!Schema::hasColumn('pantry_items', 'ingredient_id')) {
                $table->unsignedBigInteger('ingredient_id')->nullable()->after('item_name');
            }
        });

        // إضافة العمود لجدول التسوق
        Schema::table('shopping_items', function (Blueprint $table) {
            if (!Schema::hasColumn('shopping_items', 'ingredient_id')) {
                $table->unsignedBigInteger('ingredient_id')->nullable()->after('item_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pantry_items', function (Blueprint $table) {
            if (Schema::hasColumn('pantry_items', 'ingredient_id')) {
                $table->dropColumn('ingredient_id');
            }
        });

        Schema::table('shopping_items', function (Blueprint $table) {
            if (Schema::hasColumn('shopping_items', 'ingredient_id')) {
                $table->dropColumn('ingredient_id');
            }
        });
    }
};