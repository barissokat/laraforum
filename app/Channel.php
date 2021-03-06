<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Channel extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'archived' => 'boolean',
    ];

    /**
     * Boot the channels model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($thread) {
            Cache::forget('channels');
        });

        static::addGlobalScope('active', function ($builder) {
            $builder->where('archived', false);
        });

        static::addGlobalScope('sorted', function ($builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    /**
     * A channel consists of threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Archive the channel.
     */
    public function archive()
    {
        $this->update(['archived' => true]);
    }

    /**
     * Set the proper name attribute.
     *
     * @param string $value
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = Str::slug($name);
    }

    /**
     * Get a new query builder that includes archives.
     */
    public static function withArchived()
    {
        return (new static )->newQueryWithoutScope('active');
    }
}
