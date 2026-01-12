<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->string('category')->nullable()->after('name_en');
            $table->string('image')->nullable()->after('category');
            $table->boolean('is_basic')->default(false)->after('image');
            $table->boolean('is_vegan')->default(false)->after('image');
            $table->boolean('is_gluten_free')->default(false)->after('is_vegan');
            $table->boolean('is_dairy_free')->default(false)->after('is_gluten_free');  
            $table->boolean('is_nut_free')->default(false)->after('is_dairy_free');
            $table->boolean('is_fruit')->default(false)->after('is_nut_free');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'name_en',
                'category',
                'image',
                'is_basic',
                'is_vegan',
                'is_gluten_free',
                'is_dairy_free',
                'is_nut_free',
                'is_fruit'
            ]);
        });
    }
};
