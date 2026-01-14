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
                    'servings',
                    'cuisine',
                    'nutrition'
                ]); 
            }
        );
    }
};
