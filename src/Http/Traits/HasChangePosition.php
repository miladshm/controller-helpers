<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Http\Requests\ChangePositionRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithFilters;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasChangePosition
{
    use WithModel, WithFilters;

    public function changePosition(ChangePositionRequest $request, int $id)
    {
        $item = $this->model()->query()->find($id);

        $operator = $request->input('action') === 'up' ? '<' : '>';
        $second_item =
            $this->model()->newQuery()
                ->when(true, fn($q) => $this->filters($q))
                ->where($this->getPositionColumn(), $operator, $item->{$this->getPositionColumn()})->first();

        $second_item_position = $second_item->{$this->getPositionColumn()};
        $item_position = $item->{$this->getPositionColumn()};

        $item->update([
            $this->getPositionColumn() => $second_item_position
        ]);

        $second_item->update([
            $this->getPositionColumn() => $item_position
        ]);

        return ResponderFacade::respond();
    }

    protected function getPositionColumn()
    {
        return 'position';
    }
}