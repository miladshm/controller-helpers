<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

/**
 * Trait HasShow provides a show method for retrieving a single item from the database.
 *
 * @package Miladshm\ControllerHelpers\Http\Traits
 */
trait HasShow
{
    use WithModel;

    /**
     * Display the specified resource.
     *
     * @param int|string $id The unique identifier of the item to be displayed.
     * @return JsonResponse The response containing the item data.
     */
    public function show(int|string $id)
    {
        $item = $this->getItem($id);

        if ($this->getApiResource()) {
            $resource = get_class($this->getApiResource());
            return ResponderFacade::setData((new $resource($item->load($this->getRelations())))->jsonSerialize())->respond();
        }
        return ResponderFacade::setData($item->toArray())->respond();
    }
}
