<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasEdit
{
    /**
     * @return View
     */
    abstract private function editView(): View;
    abstract private function relations(): array;

    /**
     * @return Model
     */
    abstract private function model(): Model;

    /**
     * @param Model|null $item
     * @return array|null
     */
    abstract private function extraData(Model $item = null): ?array;

    /**
     * @param int $id
     * @return View|JsonResponse
     */
    public function edit(int $id): View|JsonResponse
    {
        $item = $this->model()->query()->with($this->relations())->findOrFail($id);
        if (request()->expectsJson())
            return \response()->json(compact('item') + $this->extraData($item));
        return $this->editView()->with(compact('item') + $this->extraData($item));
    }
}
