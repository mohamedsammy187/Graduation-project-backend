<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('recipes', function (Blueprint $table) {
        
        // 1. Slug (الموديل بيعمله أوتوماتيك)
        if (!Schema::hasColumn('recipes', 'slug')) {
            $table->string('slug')->nullable()->index();
        }

        // 2. Legacy Columns (عشان الموقع القديم)
        // بنستخدم longText عشان يشيل كمية بيانات كبيرة
        if (!Schema::hasColumn('recipes', 'steps')) {
            $table->longText('steps')->nullable();
        }
        if (!Schema::hasColumn('recipes', 'ingredients')) {
            $table->longText('ingredients')->nullable();
        }

        // 3. أي أعمدة إضافية ممكن تكون ناقصة
        if (!Schema::hasColumn('recipes', 'servings')) {
            $table->string('servings')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            //
        });
    }
};
