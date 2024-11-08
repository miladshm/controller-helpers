<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithExtraData;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasIndex
{
    use WithModel, WithExtraData;

    abstract private function indexView(): View;

    /**
     * @return View|JsonResponse
     */
    public function index(): View|JsonResponse
    {
        $items = $this->getItems()->get();
        if (Request::expectsJson())
            return ResponderFacade::setData(compact('items') + $this->extraData())->respond();
        return $this->indexView()->with(compact('items') + $this->extraData());
    }


    private function getItems(): Builder
    {
        return $this->model()->query()
            ->select($this->setColumns())
            ->when(count($this->relations()), function ($q){
                $q->with($this->relations());
            })
            ->when(true, function (Builder $builder){
                return $this->filters($builder);
            });
    }

    protected function setColumns() : array
    {
        return ['*'];
    }
}
