<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HigherOrderWhenProxy;

trait WithModel
{
    use WithFilters, WithRelations, WithApiResource, WithAggregates;

    /**
     * @var array|string[]
     */
    private array $columns = ['*'];

    /**
     * Retrieves a single item based on the provided ID with optimized query building.
     *
     * @param int|string $id The ID of the item to retrieve.
     * @param bool $withTrashed If true, includes soft-deleted items in the result.
     * @param bool $withFilters If true, applies filters to the query.
     * @return array|Builder|Builder[]|Collection|Model|HigherOrderWhenProxy|HigherOrderWhenProxy[]|null
     *     The retrieved item or null if not found.
     */
    protected function getItem(int|string $id, bool $withTrashed = false, bool $withFilters = true): Model|Collection|array|Builder|HigherOrderWhenProxy|null
    {
        return $this->query($withTrashed, $withFilters)
            ->findOrFail($id);
    }

    /**
     * Builds and returns an optimized query builder for the model.
     *
     * @param bool $withTrashed If true, includes soft-deleted items in the query.
     * @param bool $withFilters If true, applies filters to the query.
     * @return Builder The query builder for the model.
     */
    private function query(bool $withTrashed = false, bool $withFilters = true): Builder
    {
        $query = $this->model()->query();
        
        // Select specific columns for better performance
        $columns = $this->getColumns();
        if (!empty($columns) && $columns !== ['*']) {
            $query->select($columns);
        }
        
        // Apply soft delete handling efficiently
        if ($withTrashed && $this->modelSupportsSoftDeletes()) {
            $query->withTrashed();
        }
        
        // Apply filters when requested
        if ($withFilters) {
            $query = $this->filters($query) ?? $query;
        }
        
        // Eager load relations efficiently
        $relations = $this->getRelations();
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        // Eager load counts efficiently
        $counts = $this->getCounts();
        if (!empty($counts)) {
            $query->withCount($counts);
        }
        
        return $query;
    }

    /**
     * Check if the model supports soft deletes using caching.
     */
    private function modelSupportsSoftDeletes(): bool
    {
        static $softDeleteCache = [];
        
        $modelClass = get_class($this->model());
        
        if (!isset($softDeleteCache[$modelClass])) {
            $softDeleteCache[$modelClass] = method_exists($this->model(), 'withTrashed');
        }
        
        return $softDeleteCache[$modelClass];
    }

    /**
     * Optimized method to get multiple items with optional conditions.
     *
     * @param array $conditions
     * @param bool $withTrashed
     * @param int|null $limit
     * @return Collection
     */
    protected function getItems(array $conditions = [], bool $withTrashed = false, ?int $limit = null): Collection
    {
        $query = $this->query($withTrashed);
        
        // Apply conditions efficiently
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }
        
        // Apply limit if specified
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get a count of records with optimized query.
     *
     * @param array $conditions
     * @param bool $withTrashed
     * @return int
     */
    protected function getCount(array $conditions = [], bool $withTrashed = false): int
    {
        $query = $this->model()->query();
        
        // Only apply necessary parts for counting
        if ($withTrashed && $this->modelSupportsSoftDeletes()) {
            $query->withTrashed();
        }
        
        // Apply conditions
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }
        
        // Apply filters for count
        $query = $this->filters($query) ?? $query;
        
        return $query->count();
    }

    /**
     * Check if a record exists with optimized query.
     *
     * @param array $conditions
     * @param bool $withTrashed
     * @return bool
     */
    protected function recordExists(array $conditions, bool $withTrashed = false): bool
    {
        $query = $this->model()->query();
        
        if ($withTrashed && $this->modelSupportsSoftDeletes()) {
            $query->withTrashed();
        }
        
        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }
        
        return $query->exists();
    }

    /**
     * Specifies the model class to use.
     *
     * @return Model The model class to use.
     */
    abstract private function model(): Model;

    /**
     * Retrieves the columns to be retrieved with better memory management.
     *
     * @return array|string[] An array of column names to be retrieved.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Set columns to be selected (instance method instead of static).
     *
     * @param array $columns
     * @return self
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Reset columns to default.
     *
     * @return self
     */
    public function resetColumns(): self
    {
        $this->columns = ['*'];
        return $this;
    }

    /**
     * Add columns to the existing selection.
     *
     * @param array $columns
     * @return self
     */
    public function addColumns(array $columns): self
    {
        if ($this->columns === ['*']) {
            $this->columns = $columns;
        } else {
            $this->columns = array_unique(array_merge($this->columns, $columns));
        }
        return $this;
    }

    /**
     * Get query performance metrics (for debugging).
     *
     * @return array
     */
    public function getQueryMetrics(): array
    {
        return [
            'columns_count' => count($this->getColumns()),
            'relations_count' => count($this->getRelations()),
            'counts_count' => count($this->getCounts()),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }
}
