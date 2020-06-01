<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Mentions;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        Mentions::notifyMentionedUsers($event->reply);
    }
}
