<?php

namespace Miladshm\ControllerHelpers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladshm\ControllerHelpers\Factories\ParentModelFactory;

class ParentModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function newFactory()
    {
        return new ParentModelFactory();
    }
}
