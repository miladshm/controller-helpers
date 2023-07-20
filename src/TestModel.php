<?php

namespace Miladshm\ControllerHelpers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Miladshm\ControllerHelpers\Factories\TestModelFactory;

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = ['code'];

    protected $connection = 'testbench';

    protected static function newFactory()
    {
        return new TestModelFactory();
    }
}