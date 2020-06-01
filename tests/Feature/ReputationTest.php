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
    public function testAUserGainsPointsWhenTheyCreateAThread()
    {
        $thread = create('App\Thread');

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheyDeleteAThread()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->owner->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenTheyReplyToAThread()
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
    public function testAUserLosesPointsWhenTheirReplyToAThreadIsDeleted()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

        $this->delete(route('replies.destroy', $reply));

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenTheyReplyIsMarkedAsBest()
    {
        $thread = create('App\Thread');

        $thread->markBestReply($reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.',
        ]));

        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;

        $this->assertEquals($total, $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testWhenAThreadOwnerChangesTheirPreferredBestReplyThePointsShouldBeTransferred()
    {
        // Given a thread exists.
        $thread = create('App\Thread');

        // And we have a user, Jane.
        $jane = create('App\User');

        // If the owner of the thread marks Jane's reply as best...
        $thread->markBestReply($thread->addReply([
            'user_id' => $jane->id,
            'body' => 'Here is a reply.',
        ]));

        // Then Jane should receive the appropriate reputation points.
        $this->assertEquals(Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED, $jane->fresh()->reputation);

        // But, if the owner of the thread decides to choose a different best reply, written by John.
        $john = create('App\User');

        $thread->markBestReply($thread->addReply([
            'user_id' => $john->id,
            'body' => 'Here is a better reply.',
        ]));

        // Then, Jane's reputation should be stripped of those "best reply" points.
        $this->assertEquals(Reputation::REPLY_POSTED, $jane->fresh()->reputation);

        // And those points should now be reflected on the account of the new best reply owner.
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $john->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenWhenTheirReplyIsFavorited()
    {
        // Given we have a signed in user, John.
        $this->signIn($john = create('App\User'));

        // And also Jane...
        $jane = create('App\User');

        // If Jane adds a new reply to a thread...
        $reply = create('App\Thread')->addReply([
            'user_id' => $jane->id,
            'body' => 'Some reply',
        ]);

        // And John favorites that reply.
        $this->post(route('replies.favorite', $reply));

        // Then, Jane's reputation should grow, accordingly.
        $this->assertEquals(
            Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED,
            $jane->fresh()->reputation
        );

        // While John's should remain unaffected.
        $this->assertEquals(0, $john->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheirReplyIsUnfavorited()
    {
        // Given we have a signed in user, John.
        $this->signIn($john = create('App\User'));

        // And also Jane...
        $jane = create('App\User');

        // If Jane adds a new reply to a thread...
        $reply = create('App\Reply', ['user_id' => $jane]);

        // And John favorites that reply.
        $this->post(route('replies.favorite', $reply));

        // Then, Jane's reputation should grow, accordingly.
        $this->assertEquals(
            Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED,
            $jane->fresh()->reputation
        );

        // But, if John then unfavorites that reply...
        $this->delete(route('replies.unfavorite', $reply));

        // Then, Jane's reputation should be reduced, accordingly.
        $this->assertEquals(Reputation::REPLY_POSTED, $jane->fresh()->reputation);

        // While John's should remain unaffected.
        $this->assertEquals(0, $john->reputation);
    }
}
