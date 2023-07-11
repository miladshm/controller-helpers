<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasChangeStatus
{
    use HasModel;

    public function changeStatus(int $id, string $statusColumn = 'status')
    {
        $item = $this->model()->query()->findOrFail($id);
        $item->{$statusColumn} = !$item->{$statusColumn};
        $item->save();

        if (Request::expectsJson())
            return Response::json(Lang::get('responder::messages.success_change_status'));
        return Redirect::back()->with(Lang::get('responder::messages.success_change_status'));
    }

}
