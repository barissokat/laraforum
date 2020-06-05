<?php

namespace App\Http\Controllers;

use App\Thread;

class PinnedThreadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Pin the given thread.
     *
     * @param \App\Thread $thread
     */
    public function store(Thread $thread)
    {
        $thread->update(['pinned' => true]);
    }

    /**
     * Unpin the given thread.
     *
     * @param \App\Thread $thread
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['pinned' => false]);
    }
}
