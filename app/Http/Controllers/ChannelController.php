<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\Trending;

class ChannelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Channel  $channel
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel, Trending $trending)
    {
        if ($channel->exists) {
            $threads = $channel->threads()->latest()->paginate(25);
        } else {
            $threads = Thread::latest()->get();
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get(),
        ]);
    }
}
