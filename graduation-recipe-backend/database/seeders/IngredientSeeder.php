<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Vegetables
            ['tomato','طماطم','vegetable','asset\img\ingredients\tomato.png',1,1,1,1,1,0],
            ['onion','بصل','vegetable','asset\img\ingredients\onion.png',1,1,1,1,0,0],
            ['garlic','ثوم','vegetable','asset\img\ingredients\garlic.png',1,1,1,1,0,0],
            ['potato','بطاطس','vegetable','asset\img\ingredients\potato.png',1,1,1,1,0,0],
            ['carrot','جزر','vegetable','asset\img\ingredients\carrot.png',1,1,1,1,0,0],
            ['cucumber','خيار','vegetable','asset\img\ingredients\cucumber.png',1,1,1,1,0,0],
            ['lettuce','خس','vegetable','asset\img\ingredients\lettuce.png',1,1,1,1,0,0],

            // Proteins
            ['chicken','دجاج','protein','asset\img\ingredients\chicken.png',0,1,1,1,0,0],
            ['beef','لحم بقري','protein','asset\img\ingredients\beef.png',0,1,1,1,0,0],
            ['egg','بيض','protein','asset\img\ingredients\egg.png',0,1,1,1,0,0],
            ['fish','سمك','protein','asset\img\ingredients\fish.png',0,1,1,1,0,0],
            ['shrimp','جمبري','protein','asset\img\ingredients\shrimp.png',0,1,1,1,0,0],
            ['tuna','تونة','protein','asset\img\ingredients\tuna.png',0,1,1,1,0,0],
            // Carbs
            ['rice','أرز','carb','asset\img\ingredients\rice.png',1,1,1,1,0,0],
            ['pasta','مكرونة','carb','asset\img\ingredients\pasta.png',1,0,1,1,0,0],
            ['bread','خبز','carb','asset\img\ingredients\bread.png',1,0,1,1,0,0],
            ['bun','خبز برجر','carb','asset\img\ingredients\bun.png',1,0,1,1,0,0],
            ['flour','دقيق','carb','asset\img\ingredients\flour.png',1,0,1,1,0,0],
            // Dairy
            ['milk','حليب','dairy','asset\img\ingredients\milk.png',0,1,0,1,0,0],
            ['cheese','جبنة','dairy','asset\img\ingredients\cheese.png',0,1,0,1,0,0],
            ['butter','زبدة','dairy','asset\img\ingredients\butter.png',0,1,0,1,0,0],
            ['yogurt','زبادي','dairy','asset\img\ingredients\yogurt.png',0,1,0,1,0,0],

            // Meat
            ['meat','لحم','protein','asset\img\ingredients\meat.png',0,1,1,1,0,0],

            // Grains & Sweets
            ['oats','شوفان','carb','asset\img\ingredients\oats.png',1,1,1,1,0,0],
            ['honey','عسل','basic','asset\img\ingredients\honey.png',1,1,1,1,0,1],

            // Herbs & Seasonings
            ['herbs','أعشاب','basic','asset\img\ingredients\herbs.png',1,1,1,1,0,1],
            ['chocolate','شوكولاتة','basic','asset\img\ingredients\chocolate.png',1,1,1,1,0,1],

            // Fruits
            ['lemon','ليمون','fruit','asset\img\ingredients\lemon.png',1,1,1,1,1,0],
            ['banana','موز','fruit','asset\img\ingredients\banana.png',1,1,1,1,1,0],
            ['apple','تفاح','fruit','asset\img\ingredients\apple.png',1,1,1,1,1,0],
            ['orange','برتقال','fruit','asset\img\ingredients\orange.png',1,1,1,1,1,0],

            // Basics
            ['salt','ملح','basic','asset\img\ingredients\salt.png',1,1,1,1,0,1],
            ['pepper','فلفل','basic','asset\img\ingredients\pepper.png',1,1,1,1,0,1],
            ['sugar','سكر','basic','asset\img\ingredients\sugar.png',1,1,1,1,0,1],
            ['oil','زيت','basic','asset\img\ingredients\oil.png',1,1,1,1,0,1],
            ['olive oil','زيت زيتون','basic','asset\img\ingredients\olive_oil.png',1,1,1,1,0,1],
            ['water','ماء','basic','asset\img\ingredients\water.png',1,1,1,1,0,1],
            ['spices','توابل','basic','asset\img\ingredients\spices.png',1,1,1,1,0,1],
            ['sauce','صوص','basic','asset\img\ingredients\sauce.png',1,1,1,1,0,1],
        ];

        foreach ($ingredients as $i) {
            Ingredient::firstOrCreate(
                // ['name' => $i[0]],
                [
                    'name_en' => $i[0],
                    'name_ar' => $i[1],
                    'category' => $i[2],
                    'image' => $i[3],
                    'is_vegan' => (int)$i[4],
                    'is_gluten_free' => (int)$i[5],
                    'is_dairy_free' => (int)$i[6],
                    'is_nut_free' => (int)$i[7],
                    'is_fruit' => (int)$i[8],
                    'is_basic' => (int)$i[9],
                ]
            );
        }
    }
}
