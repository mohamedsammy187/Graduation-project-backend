<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // توسيع جميع الأعمدة النصية لتستوعب 255 حرف
        // استخدمنا MODIFY COLUMN عشان نغير خصائص العمود الموجود فعلياً
        
        DB::statement('ALTER TABLE recipes MODIFY COLUMN slug VARCHAR(255) NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN category VARCHAR(255) DEFAULT "Main Course"');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN difficulty VARCHAR(255) DEFAULT "Easy"');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN meal_type VARCHAR(255) NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN cuisine VARCHAR(255) NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN temperature VARCHAR(255) NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN servings VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // اختياري: لو حبيت ترجعهم لمساحة صغيرة
        // DB::statement('ALTER TABLE recipes MODIFY COLUMN slug VARCHAR(50) NULL');
    }
};