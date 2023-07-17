<?php

namespace Miladshm\ControllerHelpers\Factories;

use Miladshm\ControllerHelpers\TestModel;

class TestModelFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = TestModel::class;


    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [
            'code' => fake()->uuid
        ];
    }
}