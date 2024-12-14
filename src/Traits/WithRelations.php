<?php

namespace Miladshm\ControllerHelpers\Traits;

trait WithRelations
{
    protected function getRelations(): array
    {
        return (
        request()->collect(getConfigNames('params.relations'))->count()
            ? request()->collect(getConfigNames('params.relations'))
            : collect($this->relations())
        )
//            ->filter(fn($relation) => $this->model()->isRelation($relation))
            ->toArray();
    }

    protected function relations(): array
    {
        return [];
    }

}
