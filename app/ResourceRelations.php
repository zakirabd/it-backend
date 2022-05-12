<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceRelations extends Model
{
    protected $table = 'resource_relations';
    protected $fillable = [
        'resource_id',
        'role',
    ];
}
