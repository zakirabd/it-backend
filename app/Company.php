<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_avatar',
        'description',
        'address',
        'country',
        'city',
        'user_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_full_url'];

    /**
     * Get the avatar full url.
     *
     * @return string
     */
    public function getAvatarFullUrlAttribute()
    {
        if ($this->company_avatar) {
            return asset("/storage/{$this->company_avatar}");
        } else {
            return null;
        }
    }

    /**
     * Get the user that owns the company.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the users of the company.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
     * Get the courses for the company.
     */
    public function courses()
    {
        return $this->hasMany('App\CourseAssign', 'companie_id')->with('course');
    }

    /**
     * Get the expenditures for the company.
     */
    public function expenditures()
    {
        return $this->hasMany('App\Expenditure');
    }

}
