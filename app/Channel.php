<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($thread) {
    //         Cache::forget('channels');
    //     });
    // }

    /**
     * A channel consists of threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
