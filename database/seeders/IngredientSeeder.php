<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        // مصفوفة المكونات مع تصحيح أسماء الكاتيجوري لتطابق القائمة المنسدلة في المودال
        // [Name_EN, Name_AR, Category, Image, Vegan, Gluten_Free, Dairy_Free, Nut_Free, Is_Fruit, Is_Basic]
        // Vegetables, Fruits, Meat & Poultry, Dairy, Spices, Grains, Oils, Others
        $ingredients = [
            // Vegetables
            ['tomato', 'طماطم', 'Vegetables', 'asset\img\ingredients\tomato.png', 1, 1, 1, 1, 1, 0],
            ['onion', 'بصل', 'Vegetables', 'asset\img\ingredients\onion.png', 1, 1, 1, 1, 0, 0],
            ['garlic', 'ثوم', 'Vegetables', 'asset\img\ingredients\garlic.png', 1, 1, 1, 1, 0, 0],
            ['potato', 'بطاطس', 'Vegetables', 'asset\img\ingredients\potato.png', 1, 1, 1, 1, 0, 0],
            ['carrot', 'جزر', 'Vegetables', 'asset\img\ingredients\carrot.png', 1, 1, 1, 1, 0, 0],
            ['cucumber', 'خيار', 'Vegetables', 'asset\img\ingredients\cucumber.png', 1, 1, 1, 1, 0, 0],
            ['lettuce', 'خس', 'Vegetables', 'asset\img\ingredients\lettuce.png', 1, 1, 1, 1, 0, 0],

            // Proteins (Mapped to Meat & Poultry or others as needed)
            ['chicken', 'دجاج', 'Meat & Poultry', 'asset\img\ingredients\chicken.png', 0, 1, 1, 1, 0, 0],
            ['beef', 'لحم بقري', 'Meat & Poultry', 'asset\img\ingredients\beef.png', 0, 1, 1, 1, 0, 0],
            ['egg', 'بيض', 'Dairy', 'asset\img\ingredients\egg.png', 0, 1, 1, 1, 0, 0],
            ['fish', 'سمك', 'Meat & Poultry', 'asset\img\ingredients\fish.png', 0, 1, 1, 1, 0, 0],
            ['shrimp', 'جمبري', 'Meat & Poultry', 'asset\img\ingredients\shrimp.png', 0, 1, 1, 1, 0, 0],
            ['tuna', 'تونة', 'Meat & Poultry', 'asset\img\ingredients\tuna.png', 0, 1, 1, 1, 0, 0],
            ['meat', 'لحم', 'Meat & Poultry', 'asset\img\ingredients\meat.png', 0, 1, 1, 1, 0, 0],

            // Carbs / Grains
            ['rice', 'أرز', 'Grains', 'asset\img\ingredients\rice.png', 1, 1, 1, 1, 0, 0],
            ['pasta', 'مكرونة', 'Grains', 'asset\img\ingredients\pasta.png', 1, 0, 1, 1, 0, 0],
            ['bread', 'خبز', 'Grains', 'asset\img\ingredients\bread.png', 1, 0, 1, 1, 0, 0],
            ['bun', 'خبز برجر', 'Grains', 'asset\img\ingredients\bun.png', 1, 0, 1, 1, 0, 0],
            ['flour', 'دقيق', 'Grains', 'asset\img\ingredients\flour.png', 1, 0, 1, 1, 0, 0],
            ['oats', 'شوفان', 'Grains', 'asset\img\ingredients\oats.png', 1, 1, 1, 1, 0, 0],

            // Dairy
            ['milk', 'حليب', 'Dairy', 'asset\img\ingredients\milk.png', 0, 1, 0, 1, 0, 0],
            ['cheese', 'جبنة', 'Dairy', 'asset\img\ingredients\cheese.png', 0, 1, 0, 1, 0, 0],
            ['butter', 'زبدة', 'Dairy', 'asset\img\ingredients\butter.png', 0, 1, 0, 1, 0, 0],
            ['yogurt', 'زبادي', 'Dairy', 'asset\img\ingredients\yogurt.png', 0, 1, 0, 1, 0, 0],

            // Sweets & Spices
            ['honey', 'عسل', 'Spices', 'asset\img\ingredients\honey.png', 1, 1, 1, 1, 0, 1],
            ['salt', 'ملح', 'Spices', 'asset\img\ingredients\salt.png', 1, 1, 1, 1, 0, 1],
            ['pepper', 'فلفل', 'Spices', 'asset\img\ingredients\pepper.png', 1, 1, 1, 1, 0, 1],
            ['sugar', 'سكر', 'Spices', 'asset\img\ingredients\sugar.png', 1, 1, 1, 1, 0, 1],
            ['spices', 'توابل', 'Spices', 'asset\img\ingredients\spices.png', 1, 1, 1, 1, 0, 1],

            // Oils
            ['oil', 'زيت', 'Oils', 'asset\img\ingredients\oil.png', 1, 1, 1, 1, 0, 1],
            ['olive oil', 'زيت زيتون', 'Oils', 'asset\img\ingredients\olive_oil.png', 1, 1, 1, 1, 0, 1],

            // Proteins - Additional
            ['lamb', 'لحم الضأن', 'Meat & Poultry', 'asset\img\ingredients\lamb.png', 0, 1, 1, 1, 0, 0],

            // Vegetables - Additional
            ['broccoli', 'بروكلي', 'Vegetables', 'asset\img\ingredients\broccoli.png', 1, 1, 1, 1, 0, 0],
            ['bell pepper', 'الفلفل الحلو', 'Vegetables', 'asset\img\ingredients\bell_pepper.png', 1, 1, 1, 1, 0, 0],
            ['avocado', 'أفوكادو', 'Vegetables', 'asset\img\ingredients\avocado.png', 1, 1, 1, 1, 0, 0],

            // Fruits
            ['lemon', 'ليمون', 'Fruits', 'asset\img\ingredients\lemon.png', 1, 1, 1, 1, 1, 0],
            ['banana', 'موز', 'Fruits', 'asset\img\ingredients\banana.png', 1, 1, 1, 1, 1, 0],
            ['apple', 'تفاح', 'Fruits', 'asset\img\ingredients\apple.png', 1, 1, 1, 1, 1, 0],
            ['orange', 'برتقال', 'Fruits', 'asset\img\ingredients\orange.png', 1, 1, 1, 1, 1, 0],
            ['strawberry', 'فراولة', 'Fruits', 'asset\img\ingredients\strawberry.png', 1, 1, 1, 1, 1, 0],
            ['mango', 'مانجو', 'Fruits', 'asset\img\ingredients\mango.png', 1, 1, 1, 1, 1, 0],
            ['berry', 'التوت', 'Fruits', 'asset\img\ingredients\berry.png', 1, 1, 1, 1, 1, 0],

            // Spices - Additional
            ['rosemary', 'إكليل الجبل', 'Spices', 'asset\img\ingredients\rosemary.png', 1, 1, 1, 1, 0, 1],
            ['cilantro', 'كزبرة', 'Spices', 'asset\img\ingredients\cilantro.png', 1, 1, 1, 1, 0, 1],
            ['soy sauce', 'صلصة الصويا', 'Spices', 'asset\img\ingredients\soy_sauce.png', 1, 1, 1, 1, 0, 1],

            // Dairy - Additional
            ['cream cheese', 'جبنة كريمية', 'Dairy', 'asset\img\ingredients\cream_cheese.png', 0, 1, 0, 1, 0, 0],
            ['cream', 'قشطة', 'Dairy', 'asset\img\ingredients\cream.png', 0, 1, 0, 1, 0, 0],
            ['mascarpone', 'ماسكاربوني', 'Dairy', 'asset\img\ingredients\mascarpone.png', 0, 1, 0, 1, 0, 0],

            // Grains - Additional
            ['breadcrumbs', 'فتات الخبز', 'Grains', 'asset\img\ingredients\breadcrumbs.png', 1, 0, 1, 1, 0, 0],
            ['graham cracker', 'بسكويت جراهام', 'Grains', 'asset\img\ingredients\graham_cracker.png', 1, 1, 1, 1, 0, 0],
            ['ladyfinger', 'بسكويت الإصبع', 'Grains', 'asset\img\ingredients\ladyfinger.png', 1, 0, 1, 1, 0, 0],
            ['granola', 'جرانولا', 'Grains', 'asset\img\ingredients\granola.png', 1, 1, 1, 1, 0, 0],
            ['popcorn kernels', 'حبات الفشار', 'Grains', 'asset\img\ingredients\popcorn_kernels.png', 1, 1, 1, 1, 0, 0],
            ['tortilla chips', 'رقائق التورتيلا', 'Grains', 'asset\img\ingredients\tortilla_chips.png', 1, 1, 1, 1, 0, 0],

            // Beverages / Others
            ['cocoa', 'كاكاو', 'Others', 'asset\img\ingredients\cocoa.png', 1, 1, 1, 1, 0, 1],
            ['vanilla', 'فانيليا', 'Others', 'asset\img\ingredients\vanilla.png', 1, 1, 1, 1, 0, 1],
            ['coffee', 'قهوة', 'Others', 'asset\img\ingredients\coffee.png', 1, 1, 1, 1, 0, 1],
            ['coffee beans', 'حبوب القهوة', 'Others', 'asset\img\ingredients\coffee_beans.png', 1, 1, 1, 1, 0, 1],
            ['tea', 'شاي', 'Others', 'asset\img\ingredients\tea.png', 1, 1, 1, 1, 0, 1],

            // Others / Basics
            ['herbs', 'أعشاب', 'Others', 'asset\img\ingredients\herbs.png', 1, 1, 1, 1, 0, 1],
            ['chocolate', 'شوكولاتة', 'Others', 'asset\img\ingredients\chocolate.png', 1, 1, 1, 1, 0, 1],
            ['water', 'ماء', 'Others', 'asset\img\ingredients\water.png', 1, 1, 1, 1, 0, 1],
            ['sauce', 'صوص', 'Others', 'asset\img\ingredients\sauce.png', 1, 1, 1, 1, 0, 1],
            ['tomato sauce', 'صلصة الطماطم', 'Others', 'asset\img\ingredients\tomato_sauce.png', 1, 1, 1, 1, 0, 1],
            ['salsa', 'صلصة', 'Others', 'asset\img\ingredients\salsa.png', 1, 1, 1, 1, 0, 1],
            ['jalapeño', 'فلفل حار', 'Others', 'asset\img\ingredients\jalapeno.png', 1, 1, 1, 1, 0, 1],
            ['almonds', 'لوز', 'Others', 'asset\img\ingredients\almonds.png', 1, 1, 1, 1, 0, 1],
            ['raisins', 'زبيب', 'Others', 'asset\img\ingredients\raisins.png', 1, 1, 1, 1, 1, 1],
            ['dried cranberry', 'توت بري مجفف', 'Others', 'asset\img\ingredients\dried_cranberry.png', 1, 1, 1, 1, 1, 1],
            ['lime', 'ليم', 'Others', 'asset\img\ingredients\lime.png', 1, 1, 1, 1, 1, 1],
        ];

        foreach ($ingredients as $i) {
            Ingredient::updateOrCreate(
                ['name_en' => $i[0]], // التحديث بناءً على الاسم الإنجليزي
                [
                    'name_ar'        => $i[1],
                    'category'       => $i[2],
                    'image'          => $i[3],
                    'is_vegan'       => (int)$i[4],
                    'is_gluten_free' => (int)$i[5],
                    'is_dairy_free'  => (int)$i[6],
                    'is_nut_free'    => (int)$i[7],
                    'is_fruit'       => (int)$i[8],
                    'is_basic'       => (int)$i[9],
                ]
            );
        }
    }
}