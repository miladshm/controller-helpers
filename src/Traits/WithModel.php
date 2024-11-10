<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HigherOrderWhenProxy;

trait WithModel
{
    use WithFilters, WithRelations, WithApiResource;

    /**
     * @var array|string[]
     */
    private static array $columns = ['*'];

    /**
     * Retrieves a single item based on the provided ID.
     *
     * @param int|string $id The ID of the item to retrieve.
     * @param bool $withTrashed If true, includes soft-deleted items in the result.
     * @return array|Builder|Builder[]|Collection|Model|HigherOrderWhenProxy|HigherOrderWhenProxy[]|null
     *     The retrieved item or null if not found.
     */
    protected function getItem(int|string $id, bool $withTrashed = false): Model|Collection|array|Builder|HigherOrderWhenProxy|null
    {
        return $this->query($withTrashed)
            ->findOrFail($id);
    }

    /**
     * Builds and returns a query builder for the model.
     *
     * @param bool $withTrashed If true, includes soft-deleted items in the query.
     * @return Builder The query builder for the model.
     */
    private function query(bool $withTrashed = false): Builder
    {
        // Initialize the query builder
        return $this->model()->query()
            // Select the specified columns
            ->select($this->getColumns())
            // Include soft-deleted items if applicable
            ->when(
                $withTrashed && method_exists($this->model(), 'withTrashed'),
                fn(Builder $q) => $q->withTrashed()
            )
            // Eager load specified relations
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })
            // Apply filters to the query
            ->when(true, fn($q) => $this->filters($q));
    }

    /**
     * Specifies the model class to use.
     *
     * @return Model The model class to use.
     */
    abstract private function model(): Model;

    /**
     * Retrieves the columns to be retrieved.
     *
     * @return array|string[] An array of column names to be retrieved.
     */
    public function getColumns(): array
    {
        return static::$columns;
    }
}
