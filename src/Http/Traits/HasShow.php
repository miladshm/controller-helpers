<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasShow
{
    use WithModel;


    public function show($id)
    {
        $item = $this->getItem($id);

        if ($this->getApiResource()) {
            $resource = get_class($this->getApiResource());
            return ResponderFacade::setData(forward_static_call([$resource, 'make'], $item)->toArray(request()))->respond();
        }
        return ResponderFacade::setData($item->toArray())->respond();
    }

    private function getModel($id)
    {
        return $this->model()->query()
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })->findOrFail($id);
    }
}
