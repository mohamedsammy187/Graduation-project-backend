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
        Schema::table('recipes', function (Blueprint $table) {
            // بنضيف الأعمدة الجديدة بس
            // استخدمنا nullable عشان لو في داتا قديمة متبوظش

            if (!Schema::hasColumn('recipes', 'meal_type')) {
                $table->string('meal_type')->nullable();
            }
            if (!Schema::hasColumn('recipes', 'cuisine')) {
                $table->string('cuisine')->nullable();
            }
            if (!Schema::hasColumn('recipes', 'temperature')) {
                $table->string('temperature')->nullable();
            }
            if (!Schema::hasColumn('recipes', 'nutrition')) {
                $table->json('nutrition')->nullable(); // أو text لو الداتابيز قديمة
            }
            if (!Schema::hasColumn('recipes', 'category')) {
                $table->string('category')->default('Main Course');
            }
            if (!Schema::hasColumn('recipes', 'difficulty')) {
                $table->string('difficulty')->default('Easy');
            }
            // تأكد إن user_id موجود لو بتستخدمه
            if (!Schema::hasColumn('recipes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users');
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
