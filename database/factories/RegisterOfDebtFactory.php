<?php

namespace Database\Factories;

use App\Models\CollectionList;
use App\Models\RegisterOfDebt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegisterOfDebt>
 */
class RegisterOfDebtFactory extends Factory
{

    protected $model = RegisterOfDebt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'dueDate' => $this->faker->date(),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'government_id' => $this->faker->randomNumber(9),
            'collectionlist_id' => CollectionList::factory()->create()->id,
            'notified_at' => null
        ];
    }
}
