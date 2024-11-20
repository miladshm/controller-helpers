<?php

namespace Miladshm\ControllerHelpers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Miladshm\ControllerHelpers\Models\ParentModel;

/** @mixin ParentModel */
class ParentModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
