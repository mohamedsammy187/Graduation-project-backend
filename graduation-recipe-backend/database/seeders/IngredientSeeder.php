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
            ['tomato','طماطم','vegetable',1,1,1,1,1,0],
            ['onion','بصل','vegetable',1,1,1,1,0,0],
            ['garlic','ثوم','vegetable',1,1,1,1,0,0],
            ['potato','بطاطس','vegetable',1,1,1,1,0,0],
            ['carrot','جزر','vegetable',1,1,1,1,0,0],
            ['cucumber','خيار','vegetable',1,1,1,1,0,0],
            ['lettuce','خس','vegetable',1,1,1,1,0,0],

            // Proteins
            ['chicken','دجاج','protein',0,1,1,1,0,0],
            ['beef','لحم بقري','protein',0,1,1,1,0,0],
            ['egg','بيض','protein',0,1,1,1,0,0],
            ['fish','سمك','protein',0,1,1,1,0,0],
            ['shrimp','جمبري','protein',0,1,1,1,0,0],
            ['tuna','تونة','protein',0,1,1,1,0,0],

            // Carbs
            ['rice','أرز','carb',1,1,1,1,0,0],
            ['pasta','مكرونة','carb',1,0,1,1,0,0],
            ['bread','خبز','carb',1,0,1,1,0,0],
            ['bun','خبز برجر','carb',1,0,1,1,0,0],
            ['flour','دقيق','carb',1,0,1,1,0,0],

            // Dairy
            ['milk','حليب','dairy',0,1,0,1,0,0],
            ['cheese','جبنة','dairy',0,1,0,1,0,0],
            ['butter','زبدة','dairy',0,1,0,1,0,0],
            ['yogurt','زبادي','dairy',0,1,0,1,0,0],

            // Fruits
            ['lemon','ليمون','fruit',1,1,1,1,1,0],
            ['banana','موز','fruit',1,1,1,1,1,0],
            ['apple','تفاح','fruit',1,1,1,1,1,0],
            ['orange','برتقال','fruit',1,1,1,1,1,0],

            // Basics
            ['salt','ملح','basic',1,1,1,1,0,1],
            ['pepper','فلفل','basic',1,1,1,1,0,1],
            ['sugar','سكر','basic',1,1,1,1,0,1],
            ['oil','زيت','basic',1,1,1,1,0,1],
            ['olive oil','زيت زيتون','basic',1,1,1,1,0,1],
            ['water','ماء','basic',1,1,1,1,0,1],
            ['spices','توابل','basic',1,1,1,1,0,1],
            ['sauce','صوص','basic',1,1,1,1,0,1],
        ];

        foreach ($ingredients as $i) {
            Ingredient::firstOrCreate(
                ['name' => $i[0]],
                [
                    'name_en' => $i[0],
                    'name_ar' => $i[1],
                    'category' => $i[2],
                    'is_vegan' => $i[3],
                    'is_gluten_free' => $i[4],
                    'is_dairy_free' => $i[5],
                    'is_nut_free' => $i[6],
                    'is_fruit' => $i[7],
                    'is_basic' => $i[8],
                ]
            );
        }
    }
}
