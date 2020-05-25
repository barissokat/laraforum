<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @return void
     */
    public function testReplyHasAnOwner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /**
     *
     * @return void
     */
    public function testItKnowsIfItWasJustPublished()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /**
     *
     * @return void
     */
    public function testItCanDetectAllMentionedUsersInTheBody()
    {
        $reply = new \App\Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe',
        ]);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());

    }

    /**
     *
     * @return void
     */
    public function testItWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
    {
        $reply = new \App\Reply([
            'body' => 'Hello @Jane-Doe.',
        ]);

        $this->assertEquals('Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.', $reply->body);

    }

    /**
     *
     * @return void
     */
    public function testItKnowsIfItIsTheBestReply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->isBest());
    }


    /**
     *
     * @return void
     */
    public function testARepliesBodyIsSanitizedAutomatically()
    {
        $reply = make('App\Reply', [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>'
        ]);

        $this->assertEquals('<p>This is okay.</p>', $reply->body);
    }
}
