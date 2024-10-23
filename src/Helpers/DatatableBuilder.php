<?php

namespace Miladshm\ControllerHelpers\Helpers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;

class DatatableBuilder
{
    use Conditionable;

    public Builder $builder;
    private ListRequest|FormRequest $request;
    private ?array $fields;
    private ?int $pageLength;
    private ?string $order;
    private ?array $searchable;
    private ?string $paginator;

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
     * @return Paginator|Collection|CursorPaginator
     */
    public function paginate(): Paginator|Collection|CursorPaginator
    {
        $this->builder = $this->builder->select($this->fields);
        return $this->request->boolean('all')
            ? $this->builder->get()
            : $this->builder
                ->{$this->getPaginatorMethodName()}($this->getPageLength())
                ->withQueryString();
    }

    private function getPaginatorMethodName(): string
    {
        return match ($this->getPaginator()) {
            default => 'paginate',
            'simple' => 'simplePaginate',
            'cursor' => 'cursorPaginate',
        };
    }

    /**
     * @return string
     */
    public function getPaginator(): string
    {
        return $this->paginator ?? 'default';
    }

    /**
     * @param string|null $paginator
     * @return DatatableBuilder
     */
    public function setPaginator(?string $paginator = 'default'): DatatableBuilder
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageLength(): int
    {
        return $this->request->{getConfigNames('params.page_length')} ?? $this->pageLength ?? getConfigNames('default_page_length');
    }

    /**
     * @param int|null $pageLength
     * @return DatatableBuilder
     */
    public function setPageLength(?int $pageLength): DatatableBuilder
    {
        $this->pageLength = $pageLength;
        return $this;
    }

    /**
     * @return $this
     */
    public function search(): static
    {
        $q = $this->request->{getConfigNames('params.search')};
        $searchable = $this->getSearchable();
        if ($this->request->filled(getConfigNames('params.search'))) {
            $this->builder = $this->builder->where(function (Builder $s) use ($q, $searchable) {
                foreach ($searchable ?? [] as $item) {
                    if (Str::contains($item, '.')) {
                        $rel = Str::beforeLast($item, '.');
                        $column = Str::afterLast($item, '.');
                        if (method_exists($this->builder->getModel(), Str::before($rel, '.')))
                            $s->orWhereHas($rel, function (Builder $s) use ($q, $column) {
                                if (Schema::connection($s->getModel()->getConnectionName())->hasColumn($s->getModel()->getTable(), $column))
//                                    $s->where(function (Builder $s) use ($column, $q) {
//                                        foreach (explode(' ', $q) as $str)
                                    $s->where($column, 'LIKE', "%$q%");
//                                    });
                            });
                    } elseif (Schema::connection($this->builder->getModel()->getConnectionName())->hasColumn($this->builder->getModel()->getTable(), $item)) {
//                        $s->where(function (Builder $s) use ($item, $q) {
//                            foreach (explode(' ', $q) as $str)
                        $s->orWhere($item, 'LIKE', "%$q%");
//                        });
                    }
                }
            });
        }

        return $this;

    }

    /**
     * @return array|mixed|null
     */
    public function getSearchable(): mixed
    {
        return $this->request->{getConfigNames('params.searchable_columns')} ?? $this->searchable ?? getConfigNames('search.default_searchable');
    }

    /**
     * @param array|string[]|null $searchable
     * @return DatatableBuilder
     */
    public function setSearchable(?array $searchable): DatatableBuilder
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @return $this
     */
    public function sortResult(): static
    {
        if ($this->request->filled(getConfigNames('params.sort') . ".column")) {
            $sort = $this->request->{getConfigNames('params.sort')};
            $this->builder = $this->builder->orderBy($sort['column'] ?? $this->builder->getModel()->getKeyName(), $sort['dir'] ?? $this->getOrder());
        } elseif (Schema::hasColumn($this->builder->getModel()->getTable(), getConfigNames('order_column'))) {
            $this->builder = $this->builder->orderBy(getConfigNames('order_column'));
        } else
            $this->builder = $this->builder->orderBy($this->builder->getModel()->getKeyName(), $this->getOrder());

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order ?? 'desc';
    }

    /**
     * @param string|null $order
     * @return DatatableBuilder
     */
    public function setOrder(?string $order): DatatableBuilder
    {
        $this->order = $order;
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
     * @return array|string[]
     */
    public function getFields(): array
    {
        return $this->fields ?? ['*'];
    }

    /**
     * @param array|null $fields
     * @return DatatableBuilder
     */
    public function setFields(?array $fields): DatatableBuilder
    {
        $this->fields = $fields;
        return $this;
    }
}
