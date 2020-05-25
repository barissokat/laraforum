<?php

namespace App\Http\Controllers;

use App\Thread;

class LockedThreadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['admin']);
    }

    /**
     * Lock the given thread.
     *
     * @param \App\Thread $thread
     */
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);
    }

    /**
     * Unlock the given thread.
     *
     * @param \App\Thread $thread
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
