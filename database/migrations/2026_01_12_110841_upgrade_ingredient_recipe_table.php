<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {

            // كمية المكون
            $table->decimal('quantity', 8, 2)->nullable()->after('ingredient_id');

            // وحدة القياس (g, ml, cup, pcs, tbsp...)
            $table->string('unit', 20)->nullable()->after('quantity');

            // optional: نص مخصص يظهر للمستخدم
            // مثال: "2 large eggs" أو "a pinch of salt"
            $table->string('display_text')->nullable()->after('unit');

            // علشان الـ AI يعرف لو المكون أساسي ولا اختياري
            $table->boolean('is_optional')->default(false)->after('display_text');

            // اللغة العربية للمكون داخل الريسيبي
            $table->string('ingredient_name_ar')->nullable()->after('is_optional');

            // ترتيب ظهور المكونات في الوصفة
            $table->integer('sort_order')->default(0)->after('ingredient_name_ar');
        });
    }

    public function down(): void
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->dropColumn([
                'quantity',
                'unit',
                'display_text',
                'is_optional',
                'ingredient_name_ar',
                'sort_order'
            ]);
        });
    }
};
