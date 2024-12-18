<?php

namespace Miladshm\ControllerHelpers\Factories;

use Miladshm\ControllerHelpers\Models\TestModel;

class TestModelFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = TestModel::class;


    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [
            'code' => fake()->uuid,
            'order' => fake()->unique(1, 50)->numberBetween(1, 50),
            'name' => fake()->text(),
            'count' => fake()->numberBetween(1, 500),
            'status' => fake()->boolean(90)
        ];
    }
}