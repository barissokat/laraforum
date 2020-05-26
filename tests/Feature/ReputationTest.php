<?php

namespace Tests\Feature;

use App\Reputation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyCreateAThread()
    {
        $thread = create('App\Thread');

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyReplyToAThread()
    {
        $thread = create('App\Thread');

        $reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.',
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyReplyIsMarkedAsBest()
    {
        $thread = create('App\Thread');

        $reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.',
        ]);

        $reputation = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;

        $thread->markBestReply($reply);

        $this->assertEquals($reputation, $reply->owner->reputation);
    }
}
