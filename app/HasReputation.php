<?php

namespace App;

trait HasReputation
{
    /**
     * Award reputation points to the model.
     *
     * @param  string $action
     */
    public function gainReputation($action)
    {
        $this->increment('reputation', config("laraforum.reputation.{$action}"));
    }

    /**
     * Award reputation points to the model.
     *
     * @param  string $action
     */
    public function loseReputation($action)
    {
        $this->decrement('reputation', config("laraforum.reputation.{$action}"));
    }
}
