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
            ->toArray();
    }

    protected function relations(): array
    {
        return [];
    }

}
