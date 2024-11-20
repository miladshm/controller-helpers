<?php

namespace Miladshm\ControllerHelpers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladshm\ControllerHelpers\Factories\TestRelModelFactory;

class TestRelModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_model_id',
        'name',
    ];

    public static function factory($count = null, $state = [])
    {
        return new TestRelModelFactory();
    }

    public function testModel()
    {
        return $this->belongsTo(TestModel::class);
    }
}
