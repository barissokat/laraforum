<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use Illuminate\Support\Facades\Gate;

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

    public function store($channelId, Thread $thread)
    {
        if (Gate::denies('create', Reply::class)) {
            return response('You are posting too frequently. Please take a break.', 429);
        }

        try {
            request()->validate([
                'body' => 'required|spamfree',
            ]);

            $reply = $thread->addReply([
                'user_id' => auth()->id(),
                'body' => request('body'),
            ]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422);
        }

        return $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {
            request()->validate([
                'body' => 'required|spamfree',
            ]);

            $reply->update(request(['body']));
        } catch (\Exception $th) {
            return response('Sorry, your reply could not be saved at this time.', 422);
        }

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
