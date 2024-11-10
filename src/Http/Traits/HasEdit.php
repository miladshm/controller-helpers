<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasEdit
{
    use WithModel;
    /**
     * This method should return the view instance for the edit page.
     *
     * @return View
     */
    abstract private function editView(): View;

    /**
     * @return Model This method should return an array of model relations as strings.
     *
     * This method should return an array of model relations as strings.
     * For example, if the model has a relation "category" and another relation "tags",
     * the method should return ["category", "tags"].
     *
     * The relations will be eager loaded in the controller.
     */
    abstract private function model(): Model;

    /**
     * Provides additional data to be included with the item.
     *
     * This method should be implemented to return an array of extra data
     * associated with the given item. If no item is provided, it should
     * return null or an array of default data.
     *
     * @param Model|null $item The model instance for which extra data is needed.
     * @return array|null An array containing extra data or null if no data is available.
     */
    abstract private function extraData(Model $item = null): ?array;

    /**
     * Handles the edit functionality for a specific item.
     *
     * @param int $id The unique identifier of the item to be edited.
     * @return View|JsonResponse Returns a view for editing the item if the request expects HTML,
     *                          otherwise returns a JSON response with the item's data.
     */
    public function edit(int $id): View|JsonResponse
    {
        $item = $this->getItem($id);
        if (request()->expectsJson())
            return \response()->json(compact('item') + $this->extraData($item));
        return $this->editView()->with(compact('item') + $this->extraData($item));
    }
}
