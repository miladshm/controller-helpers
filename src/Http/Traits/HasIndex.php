<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasIndex
{
    abstract private function indexView(): View;
    abstract private function model(): Model;
    abstract private function relations(): array;
    abstract private function extraData(Model $item = null): ?array;

    /**
     * @return View|JsonResponse
     */
    public function index(): View|JsonResponse
    {
        $items = $this->getItems()->get();
        if (request()->expectsJson())
            return \response()->json(compact('items') + $this->extraData());
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

    protected function filters(Builder $builder) : null|Builder
    {
        return null;
    }

    protected function setColumns() : array
    {
        return ['*'];
    }
}
