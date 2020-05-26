<?php

namespace Tests\Feature;

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

        $this->assertEquals(10, $thread->owner->reputation);
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
            'body' => 'Here is a reply.'
        ]);

        $this->assertEquals(2, $reply->owner->reputation);
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
            'body' => 'Here is a reply.'
        ]);

        $this->assertEquals(2, $reply->owner->reputation);

        $thread->markBestReply($reply);

        $this->assertEquals(52, $reply->owner->reputation);
    }
}
