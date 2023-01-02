<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait HasDestroy
{
    abstract private function model():Model;


    /**
     * @param $id
     * @return JsonResponse|RedirectResponse
     * @throws \Throwable
     */
    public function destroy($id): JsonResponse|RedirectResponse
    {
            $item = $this->model()->query()
                ->when(in_array(SoftDeletes::class,class_uses($this->model()) ?? []), function (Builder $q){
                    $q->withTrashed();
                })
                ->findOrFail($id);

            if ($item->deleted_at)
                $item->forceDelete();
            else
                $item->deleteOrFail();

        if (request()->expectsJson())
            return ResponderFacade::setMessage(trans('messages.success_delete.status'))->respond();
        return back()->with(trans('messages.success_delete'));
    }
}
