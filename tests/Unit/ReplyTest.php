<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function replyHasAnOwner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itKnowsIfItWasJustPublished()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /**
     * @test
     *
     * @return void
     */
    public function itCanDetectAllMentionedUsersInTheBody()
    {
        $reply = new \App\Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe',
        ]);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());

    }

    /**
     * @test
     *
     * @return void
     */
    public function itWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
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
}
