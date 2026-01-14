<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PantryItem;

class PantrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PantryItem::create([
            'user_id' => 1,
            'item_name' => 'Tomato',
            'ingredient_id' => 1,
            'added_date' => now(),
        ]);
        PantryItem::create([
            'user_id' => 1,
            'item_name' => 'Rice',
            'ingredient_id' => 2,
            'added_date' => now(),
        ]);
        PantryItem::create([
            'user_id' => 1,
            'item_name' => 'Salt',
            'ingredient_id' => 3,
            'added_date' => now(),
        ]);
    }
}
