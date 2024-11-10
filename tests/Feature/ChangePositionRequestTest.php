<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;


use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Tests\TestCase;

class ChangePositionRequestTest extends TestCase
{
    public function test_move_up()
    {
        $id = rand(1, 50);
        $item = TestModel::query()->find($id);
        $item_order = $item->order;
        $second = TestModel::query()->orderBy('order', 'desc')->where('order', '<', $item_order)->first();
        $second_order = $second->order;

        $this->postJson("change-position/$id", ['action' => 'up']);

        $item->order = $second_order;
        $second->order = $item_order;

        $this->assertModelExists($item);
        $this->assertModelExists($second);

    }

    public function test_move_down()
    {
        $id = rand(1, 50);
        $item = TestModel::query()->find($id);
        $item_order = $item->order;
        $second = TestModel::query()->orderBy('order',)->where('order', '>', $item_order)->first();
        $second_order = $second->order;

        $this->postJson("change-position/$id", ['action' => 'down']);

        $item->order = $second_order;
        $second->order = $item_order;

        $this->assertModelExists($item);
        $this->assertModelExists($second);
    }
}
