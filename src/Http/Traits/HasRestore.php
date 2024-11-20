<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasRestore
{
    use WithModel;

    public function restore($id)
    {
        $item = $this->getItem($id, true);

        $item->restore();

        if (Request::expectsJson())
            return ResponderFacade::setMessage(Lang::get('responder::messages.success_restore.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_restore'));

    }

}
