<?php

namespace Miladshm\ControllerHelpers\Traits;

trait WithAggregates
{
    protected function getCounts(): array
    {
        return (
        request()->collect(getConfigNames('params.counts'))->count()
            ? request()->collect(getConfigNames('params.counts'))
            : collect($this->counts())
        )
            ->filter(fn($relation) => $this->model()->isRelation($relation))
            ->toArray();
    }

    protected function counts(): array
    {
        return request()->collect(getConfigNames('params.counts'))->toArray();
    }

}
