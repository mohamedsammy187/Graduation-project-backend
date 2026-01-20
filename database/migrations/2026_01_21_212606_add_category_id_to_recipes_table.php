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
        if (!Schema::hasColumn('recipes', 'category_id')) {
            // هنا بقى هنستخدم constrained عادي لأن الجدول أصبح موجود!
            $table->foreignId('category_id')
                  ->nullable() // يقبل null للوصفات القديمة
                  ->constrained('categories') // بيربط بجدول categories
                  ->nullOnDelete(); // لو حذفنا القسم، الوصفة تفضل موجودة بس القسم يبقى null
        }
    });
}

public function down()
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->dropForeign(['category_id']);
        $table->dropColumn('category_id');
    });
}
};
