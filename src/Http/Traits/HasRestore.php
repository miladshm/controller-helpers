<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;

trait HasRestore
{
    abstract private function model(): Model;

    public function restore($id)
    {
        $item = $this->model()->withTrashed()->findOrFail($id);

        $item->restore();

        if (Request::expectsJson())
            return ResponderFacade::setMessage(Lang::get('responder::messages.success_restore.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_restore'));

    }

}
