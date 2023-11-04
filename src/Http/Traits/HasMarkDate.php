<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Http\Requests\MarkRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasMarkDate
{
    use WithModel;


    /**
     * @param MarkRequest $request
     * @param int $id
     * @param string|null $mark_field
     * @return JsonResponse
     */
    public function mark(MarkRequest $request, int $id, ?string $mark_field = null): JsonResponse
    {
        $item = $this->model()->query()->findOrFail($id);

        $field = $mark_field ?? $this->getMarkField();

        if (!Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $field))
            throw ValidationException::withMessages([$field => trans('responder::messages.field_not_exists')]);

        if ($item->{$field})
            throw ValidationException::withMessages([$field => trans('responder::messages.field_already_marked')]);

        $item->update([
            $field => $request->date('time')
        ]);

        return ResponderFacade::setData($item)->respond();
    }


    protected function getMarkField(): string
    {
        return 'changed_at';
    }

}
