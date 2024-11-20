<?php

namespace Miladshm\ControllerHelpers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Miladshm\ControllerHelpers\Factories\TestModelFactory;

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $connection = 'testbench';

    protected static function newFactory()
    {
        return new TestModelFactory();
    }

    public function rels()
    {
        return $this->hasMany(TestRelModel::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}