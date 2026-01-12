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
        Schema::table('recipes', function (Blueprint $table){
            $table->string('title_ar')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->integer('servings')->default(1)->after('calories');
            $table->string('cuisine')->nullable()->after('difficulty');// example: Italian, Chinese, Mexican
            $table->json('nutrition')->nullable()->after('cuisine'); // example: carbs, protein, fat details
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('recipes', function (Blueprint $table){
                $table->dropColumn([
                    'title_ar',
                    'title_en',
                    'description_en',
                    'description_ar',
                    'servings',
                    'cuisine',
                    'nutrition'
                ]); 
            }
        );
    }
};
