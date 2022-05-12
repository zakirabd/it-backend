<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';
    protected $fillable = [
        'title',
        'status',
        'attachment',
    ];
    public function resource_relations()
    {
        return $this->hasMany( ResourceRelations::class);
    }





    protected $appends = ['image_full_url'];
    public function getImageFullUrlAttribute()
    {
        if ($this->attachment) {
            return asset("/storage/{$this->attachment}");
        } else {
            return null;
        }

    }
}
