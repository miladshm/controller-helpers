<?php

namespace Miladshm\ControllerHelpers\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Miladshm\ControllerHelpers\TestModel;

/** @mixin TestModel */
class TestModelResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'order' => $this->order,
            'count' => $this->count,
        ];
    }
}
