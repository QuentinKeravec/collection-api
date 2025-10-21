<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['movie','book','game','manga','anime','music'];
        return [
            'title'=>fake()->sentence(3),
            'type'=>fake()->randomElement($types),
            'year'=>fake()->numberBetween(1980,2025),
            'author'=>fake()->name(),
            'description'=>fake()->paragraph(),
        ];
    }
}
