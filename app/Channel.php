<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Channel extends Model
{
    protected $guarded = [];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($thread) {
    //         Cache::forget('channels');
    //     });
    // }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
