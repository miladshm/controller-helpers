<?php

namespace Miladshm\ControllerHelpers\Helpers;

use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DatatableBuilder
{
    public Builder $builder;
    private ListRequest $request;
    private array $fields = ['*'];
    private int $pageLength = 10;
    private array $searchable = ['id','title', 'name', 'body', 'description'];

    public function __construct()
    {
    }


    /**
     * @param null $pageLength
     * @return Paginator
     */
    private function paginate($pageLength = null): Paginator
    {
        return $this->builder->select($this->fields)->paginate($this->request->pageLength ?? $pageLength);
    }



    /**
     * @param ListRequest $request
     * @param Model|null $model
     * @return Paginator
     */
    public function grid(ListRequest $request, Model $model = null)
    {
        $this->builder = $model?->newQuery() ?? $this->builder;
        $this->request = $request;
        $results = $this->search()->sortResult();

        if ($request->boolean('all'))
            return $results->builder->get();
        else
            return $results->paginate($request->pageLength ?? $this->pageLength)
                ->withQueryString();
    }


    /**
     * @return $this
     */
    private function search(): static
    {
        $q = $this->request->q;
        $searchable = $this->request->searchable ?? $this->searchable;
        if ($this->request->filled('q')) {
            $this->builder = $this->builder->where(function (Builder $s) use ( $q, $searchable) {
                foreach ($searchable as $item) {
                    if (Str::contains($item, '.')) {
                        $rel = Str::before($item,'.');
                        $column = Str::after($item,'.');
                        if (method_exists($this->builder->getModel(), $rel))
                            $s->orWhereHas($rel, function ($s) use ($q, $column) {
                                if(Schema::hasColumn($s->getModel()->getTable(), $column))
                                    $s->where($column, 'LIKE', '%' . $q . '%');
                            });
                    }
                    elseif (Schema::hasColumn($this->builder->getModel()->getTable(), $item))
                        $s->orwhere($item, 'LIKE', '%' . $q . '%');
                }
            });
        }

        return $this;

    }

    /**
     * @return $this
     */
    private function sortResult(): static
    {
        if ($this->request->filled('sort.column')) {
            $sort = $this->request->sort;
            $this->builder = $this->builder->orderBy($sort['column'] ?? "created_at", $sort['dir'] ?? "desc");
        } elseif (Schema::hasColumn($this->builder->getModel()->getTable(),'order')) {
            $this->builder = $this->builder->orderBy('order');
        } else
            $this->builder = $this->builder->latest('id');

        return $this;
    }

    /**
     * @param Builder $builder
     * @return DatatableBuilder
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @param int|null $pageLength
     * @return DatatableBuilder
     */
    public function setPageLength(?int $pageLength): DatatableBuilder
    {
        $this->pageLength = $pageLength ?? $this->pageLength;
        return $this;
    }

    /**
     * @param array|string[]|null $searchable
     * @return DatatableBuilder
     */
    public function setSearchable(?array $searchable): DatatableBuilder
    {
        $this->searchable = $searchable ?? $this->searchable;
        return $this;
    }

    /**
     * @param array|null $fields
     * @return DatatableBuilder
     */
    public function setFields(?array $fields): DatatableBuilder
    {
        $this->fields = $fields ?? ['*'];
        return $this;
    }
}
