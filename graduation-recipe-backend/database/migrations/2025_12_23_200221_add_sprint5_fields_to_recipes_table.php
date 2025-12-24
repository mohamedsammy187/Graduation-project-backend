<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->enum('category', ['breakfast', 'lunch', 'dinner', 'drink', 'snack'])
              ->after('title');
        $table->string('meal_type')->nullable()->after('category');
        $table->enum('temperature', ['hot', 'cold'])->nullable()->after('meal_type');
    });
}

public function down(): void
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->dropColumn(['category', 'meal_type', 'temperature']);
    });
}

};
