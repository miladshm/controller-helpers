<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait WithApiResource
{
    public function getApiResource()
    {
        if (!getConfigNames('resources.enabled')) return null;
        if ($this->getJsonResourceClass() !== null) return $this->getJsonResourceClass();

        $model_name = class_basename($this->model());

        return app_path("Http/Resources/{$model_name}Resource");
    }

    public function getJsonResourceClass(): ?JsonResource
    {
        return null;
    }
}
