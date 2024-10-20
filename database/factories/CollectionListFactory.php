<?php

namespace Database\Factories;

use App\Models\CollectionList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollectionList>
 */
class CollectionListFactory extends Factory
{
    protected $model = CollectionList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->word . '.csv';

        return [
            'original_name' => $filename,
            'name' => $filename,
            'path' => 'collection-lists-' . now()->format('Y-m-d') . '/' . $filename,
            'processed_at' => null,
        ];
    }
}
