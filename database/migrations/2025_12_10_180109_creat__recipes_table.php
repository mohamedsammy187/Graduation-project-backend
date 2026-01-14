<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            // $table->string('title');
            // $table->string('category');           // must provide in seeder
            $table->string('image')->nullable();
            $table->string('time');
            $table->string('difficulty');
            $table->string('calories');
            $table->longText('ingredients');      // store JSON
            $table->longText('steps');            // store JSON
            $table->timestamps();                 // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
