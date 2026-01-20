<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الأقسام الأساسية أولاً لضمان وجود IDs للوصفات
        Category::updateOrCreate(['id' => 1], ['name_en' => 'Meal', 'name_ar' => 'وجبة']);
        Category::updateOrCreate(['id' => 2], ['name_en' => 'Dessert', 'name_ar' => 'حلويات']);
        Category::updateOrCreate(['id' => 3], ['name_en' => 'Drink', 'name_ar' => 'مشروب']);
        Category::updateOrCreate(['id' => 4], ['name_en' => 'Snack', 'name_ar' => 'سناك']);

        // 2. إنشاء الأدمن تلقائياً
        \App\Models\User::updateOrCreate(
            ['email' => 'gabrielsorour51@gmail.com'],
            [
                'name' => 'Gabriel Sorour',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );

        // 3. استدعاء باقي السيدرز بالترتيب الصحيح
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class, // تم تعديله ليربط بالـ Categories
            PantrySeeder::class,
            IngredientRecipeSeeder::class
        ]);
    }
}