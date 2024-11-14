<?php

namespace Miladshm\ControllerHelpers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Miladshm\ControllerHelpers\Models\TestModel */
class TestModelCollection extends ResourceCollection
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            $this->collection,
        ];
    }
}
