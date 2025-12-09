<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'image' => 'https://picsum.photos/300',
            'time' => $this->faker->numberBetween(10, 60) . ' min',
            'difficulty' => $this->faker->randomElement(['Easy', 'Medium', 'Hard']),
            'calories' => $this->faker->numberBetween(150, 600) . ' kcal',
            'ingredients' => json_encode($this->faker->randomElements(
                ['Tomato', 'Basil', 'Garlic', 'Onion', 'Chicken', 'Cream', 'Rice', 'Cheese'],
                3
            )),
            'steps' => json_encode([
                'Step 1: ' . $this->faker->sentence(),
                'Step 2: ' . $this->faker->sentence(),
                'Step 3: ' . $this->faker->sentence()
            ])
        ];
    }
}
