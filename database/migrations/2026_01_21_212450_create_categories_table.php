<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // ده بيعمل id autoincrement
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->string('imagepath')->nullable(); // عشان انت كاتبه كدا في الموديل
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
