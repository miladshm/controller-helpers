<?php

namespace Miladshm\ControllerHelpers\Helpers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;

class DatatableBuilder
{
    public Builder $builder;
    private ListRequest|FormRequest $request;
    private array $fields = ['*'];
    private int $pageLength = 10;
    private string $order = 'desc';
    private ?array $searchable;

    public function __construct()
    {
        $this->searchable = getConfigNames('search.default_searchable');
    }

    /**
     * @param FormRequest|ListRequest $request
     * @return DatatableBuilder
     */
    public function setRequest(FormRequest|ListRequest $request): DatatableBuilder
    {
        $this->request = $request;
        return $this;
    }


    /**
     * @return Paginator|Collection
     */
    public function paginate(): Paginator|Collection
    {
        $this->builder = $this->builder->select($this->fields);
        return $this->request->boolean('all')
            ? $this->builder->get()
            : $this->builder
                ->paginate($this->request->{getConfigNames('params.page_length')} ?? $this->pageLength)
                ->withQueryString();
    }




    /**
     * @return $this
     */
    public function search(): static
    {
        $q = $this->request->{getConfigNames('params.search')};
        $searchable = $this->request->{getConfigNames('params.searchable_columns')} ?? $this->searchable;
        if ($this->request->filled(getConfigNames('params.search'))) {
            $this->builder = $this->builder->where(function (Builder $s) use ($q, $searchable) {
                foreach ($searchable ?? [] as $item) {
                    if (Str::contains($item, '.')) {
                        $rel = Str::before($item, '.');
                        $column = Str::after($item, '.');
                        if (method_exists($this->builder->getModel(), $rel))
                            $s->orWhereHas($rel, function ($s) use ($q, $column) {
                                if (Schema::hasColumn($s->getModel()->getTable(), $column))
                                    $s->where($column, 'LIKE', '%' . $q . '%');
                            });
                    } elseif (Schema::hasColumn($this->builder->getModel()->getTable(), $item))
                        $s->orwhere($item, 'LIKE', '%' . $q . '%');
                }
            });
        }

        return $this;

    }

    /**
     * @return $this
     */
    public function sortResult(): static
    {
        if ($this->request->filled(getConfigNames('params.sort') . ".column")) {
            $sort = $this->request->{getConfigNames('params.sort')};
            $this->builder = $this->builder->orderBy($sort['column'] ?? $this->builder->getModel()->getKeyName(), $sort['dir'] ?? "desc");
        } elseif (Schema::hasColumn($this->builder->getModel()->getTable(), getConfigNames('order_column'))) {
            $this->builder = $this->builder->orderBy(getConfigNames('order_column'));
        } else
            $this->builder = $this->builder->orderBy($this->builder->getModel()->getKeyName(), $this->order);

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

    /**
     * @param string $order
     * @return DatatableBuilder
     */
    public function setOrder(string $order): DatatableBuilder
    {
        $this->order = $order;
        return $this;
    }
}
