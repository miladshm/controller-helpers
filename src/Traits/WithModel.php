<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithModel
{
    /**
     * Specifying the model class to use
     *
     * @return Model
     */
    abstract private function model(): Model;

}