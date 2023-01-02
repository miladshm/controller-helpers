<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasChangeStatus
{
    abstract private function model(): Model;

    public function changeStatus(int $id)
    {
        $item = $this->model()->query()->findOrFail($id);
        $item->status = !$item->status;
        $item->save();

        if (\request()->expectsJson())
            return response()->json(trans('messages.success_change_status') );
        return redirect()->back()->with(trans('messages.success_change_status'));
    }

}
