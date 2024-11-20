<?php

namespace Miladshm\ControllerHelpers\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Models\TestRelModel;

class TestRelModelFactory extends Factory
{
    protected $model = TestRelModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'test_model_id' => TestModel::factory(),
        ];
    }
}
