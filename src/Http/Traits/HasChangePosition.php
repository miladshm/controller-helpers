<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Http\Requests\ChangePositionRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

/**
 * This trait provides a method to change the position of an item in a sorted list.
 * It uses the provided ChangePositionRequest to determine the direction of movement.
 *
 * @package Miladshm\ControllerHelpers\Http\Traits
 */
trait HasChangePosition
{
    use WithModel;

    /**
     * Changes the position of an item in a sorted list.
     *
     * This method updates the position of an item based on the action specified in the request.
     * It swaps the positions of the item with another item in the specified direction ('up' or 'down').
     *
     * @param ChangePositionRequest $request The request containing the action (up or down).
     * @param int $id The ID of the item to be moved.
     * @return JsonResponse The response from the ResponderFacade.
     * @throws ValidationException If the item cannot be moved in the specified direction.
     */
    public function changePosition(ChangePositionRequest $request, int $id)
    {
        // Retrieve the item to be moved using the provided ID
        $item = $this->model()->query()->findOrFail($id);

        // Determine the comparison operator and sort order based on the action
        $operator = $request->input('action') === 'up' ? '<' : '>';
        $order = $request->input('action') === 'up' ? 'desc' : 'asc';

        // Find the adjacent item to swap positions with
        $second_item = $this->model()->newQuery()
            ->when(true, fn($q) => $this->filters($q))
            ->where($this->getPositionColumn(), $operator, $item->{$this->getPositionColumn()})
            ->orderBy($this->getPositionColumn(), $order)
            ->firstOr(function () use ($request) {
                // Throw a validation exception if there's no item to swap with
                $message = $request->input('action') === 'up'
                    ? Lang::get('responder::messages.cannot_lift_up')
                    : Lang::get('responder::messages.cannot_get_down');

                throw ValidationException::withMessages([$this->getPositionColumn() => $message]);
            });

        // Store the positions of both items
        $second_item_position = $second_item->{$this->getPositionColumn()};
        $item_position = $item->{$this->getPositionColumn()};

        // Swap the positions of the two items
        $item->update([
            $this->getPositionColumn() => $second_item_position
        ]);

        $second_item->update([
            $this->getPositionColumn() => $item_position
        ]);

        // Return a response indicating the operation was successful
        return ResponderFacade::respond();
    }

    /**
     * Retrieves the name of the column used for sorting.
     *
     * @return string The name of the position column.
     */
    protected function getPositionColumn()
    {
        return getConfigNames('order_column');
    }
}
