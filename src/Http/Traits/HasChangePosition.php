<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Http\Requests\ChangePositionRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasChangePosition
{
    use WithModel;

    public function changePosition(ChangePositionRequest $request, int $id)
    {
        $item = $this->model()->query()->findOrFail($id);

        $operator = $request->input('action') === 'up' ? '<' : '>';
        $order = $request->input('action') === 'up' ? 'desc' : 'asc';
        $second_item =
            $this->model()->newQuery()
                ->when(true, fn($q) => $this->filters($q))
                ->where($this->getPositionColumn(), $operator, $item->{$this->getPositionColumn()})
                ->orderBy($this->getPositionColumn(), $order)
                ->firstOr(function () use ($request) {
                    $message = $request->input('action') === 'up'
                        ? Lang::get('responder::messages.cannot_lift_up')
                        : Lang::get('responder::messages.cannot_get_down');

                    throw ValidationException::withMessages([$this->getPositionColumn() => $message]);
                });

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
        return getConfigNames('order_column');
    }
}
