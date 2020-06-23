<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testReplyHasAnOwner()
    {
        $reply = create(Reply::class);

        $this->assertInstanceOf(User::class, $reply->owner);
    }

    /**
     *
     * @return void
     */
    public function testItKnowsIfItWasJustPublished()
    {
        $reply = create(Reply::class);

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /**
     *
     * @return void
     */
    public function testItWrapsMentionedUsernamesInTheBodyWithinAnchorTags()
    {
        $reply = new Reply([
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
        $reply = create(Reply::class);

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
        $reply = make(Reply::class, [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals('<p>This is okay.</p>', $reply->body);
    }
}
