<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HigherOrderWhenProxy;

trait WithModel
{
    use WithFilters, WithRelations, WithApiResource;

    /**
     * @param int $id
     * @param bool $withTrashed
     * @return array|Builder|Builder[]|Collection|Model|HigherOrderWhenProxy|HigherOrderWhenProxy[]|null
     */
    protected function getItem(int $id, bool $withTrashed = false): Model|Collection|array|Builder|HigherOrderWhenProxy|null
    {
        return $this->model()
            ->query()
            ->when(
                $withTrashed && in_array(SoftDeletes::class, class_uses($this->model()) ?? []),
                fn(Builder $q) => $q->withTrashed()
            )
            ->when(count($this->relations()), fn($q) => $q->with($this->relations()))
            ->when(true, fn($q) => $this->filters($q))
            ->findOrFail($id);
    }

    private function getItems(): Builder
    {
        return $this->model()->query()
            ->select($this->getColumns())
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            });
    }

    /**
     * Specifying the model class to use
     *
     * @return Model
     */
    abstract private function model(): Model;
}
