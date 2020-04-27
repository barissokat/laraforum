<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Reply;
use App\Thread;

class ReplyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread, Spam $spam)
    {

        try {
            request()->validate([
                'body' => 'required',
            ]);

            $spam->detect(request('body'));

            $reply = $thread->addReply([
                'user_id' => auth()->id(),
                'body' => request('body'),
            ]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422);
        }

        return $reply->load('owner');
    }

    public function update(Reply $reply, Spam $spam)
    {
        try {
            $this->authorize('update', $reply);

            request()->validate([
                'body' => 'required',
                ]);

                $spam->detect(request('body'));
            } catch (\Throwable $th) {
                return response('Sorry, your reply could not be saved at this time.', 422);
        }

        $reply->update(request(['body']));
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back()->with('flash', 'Your reply has been deleted.');
    }
}
