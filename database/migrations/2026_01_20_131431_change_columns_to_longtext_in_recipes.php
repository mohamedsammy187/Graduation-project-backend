<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ✅ ضروري جداً عشان نستخدم الأوامر المباشرة

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // بنعدل نوع الأعمدة لتصبح LONGTEXT عشان تستوعب أي كمية نصوص أو JSON
        // بنستخدم MODIFY COLUMN عشان نغير النوع لنفس العمود الموجود
        
        DB::statement('ALTER TABLE recipes MODIFY COLUMN steps LONGTEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN ingredients LONGTEXT NULL');
        
        // برضه بنوسع الوصف والنوتريشن للاحتياط
        DB::statement('ALTER TABLE recipes MODIFY COLUMN description_en LONGTEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN description_ar LONGTEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN nutrition LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لو حبيت ترجع في التعديل (اختياري)
        // بنرجعهم لنوع TEXT العادي
        DB::statement('ALTER TABLE recipes MODIFY COLUMN steps TEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN ingredients TEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN description_en TEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN description_ar TEXT NULL');
        DB::statement('ALTER TABLE recipes MODIFY COLUMN nutrition TEXT NULL');
    }
};