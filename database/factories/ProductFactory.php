<?php

namespace Database\Factories;

use App\Models\products;
use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\products>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = products::class;

    public function definition(): array
    {
        return [
            //'categories_id' => CategoryFactory::new()->create()->id,
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(1, 100, 2),
            'image' => $this->faker->imageUrl,
        ];
    }
}
