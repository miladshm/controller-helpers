<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

trait WithApiResource
{
    public function getApiResource(): ?JsonResource
    {
        if (!getConfigNames('resources.enabled')) return null;

        return is_string($this->getJsonResourceClass()) ? App::make($this->getJsonResourceClass()) : $this->getJsonResourceClass();

    }

    /**
     * @return JsonResource|class-string|null
     */
    public function getJsonResourceClass(): JsonResource|string|null
    {
        return null;
    }

//    public function getApiCollection(): ?ResourceCollection
//    {
//        if (!getConfigNames('resources.enabled')) return null;
//
//        return is_string($this->getJsonCollectionClass()) ? App::make($this->getJsonCollectionClass()) : $this->getJsonCollectionClass();
//
//    }
//
//    /**
//     * @return JsonResource|class-string|null
//     */
//    public function getJsonCollectionClass(): ResourceCollection|string|null
//    {
//        return null;
//    }
}
