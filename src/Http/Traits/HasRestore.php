<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasRestore
{
    abstract private function model(): Model;

    public function restore($id)
    {
        $item = $this->model()->withTrashed()->findOrFail($id);

        $item->restore();

        return response()->json(trans('messages.success_restore'));

    }

}
