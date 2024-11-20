<?php

namespace Miladshm\ControllerHelpers\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Miladshm\ControllerHelpers\Models\ParentModel;

class ParentModelFactory extends Factory
{
    protected $model = ParentModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
