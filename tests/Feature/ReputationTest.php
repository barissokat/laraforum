<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    protected $points = [];

    /**
     * Fetch current reputation points on class initialization.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->points = config('laraforum.reputation');
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenTheyCreateAThread()
    {
        $thread = create(Thread::class);

        $this->assertEquals($this->points['thread_published'], $thread->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheyDeleteAThread()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->assertEquals($this->points['thread_published'], $thread->owner->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenTheyReplyToAThread()
    {
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.',
        ]);

        $this->assertEquals($this->points['reply_posted'], $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheirReplyToAThreadIsDeleted()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $this->assertEquals($this->points['reply_posted'], $reply->owner->reputation);

        $this->delete(route('replies.destroy', $reply));

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenTheyReplyIsMarkedAsBest()
    {
        $thread = create(Thread::class);

        $thread->markBestReply($reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.',
        ]));

        $total = $this->points['reply_posted'] + $this->points['best_reply_awarded'];

        $this->assertEquals($total, $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheirBestReplyIsDeleted()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $total = $this->points['reply_posted'] + $this->points['best_reply_awarded'];
        $this->assertEquals($total, auth()->user()->fresh()->reputation);

        $reply->delete();

        $this->assertEquals(0, auth()->user()->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testWhenAThreadOwnerChangesTheirPreferredBestReplyThePointsShouldBeTransferred()
    {
        // Given a thread exists.
        $thread = create(Thread::class);

        // And we have a user, Jane.
        $jane = create(User::class);

        // If the owner of the thread marks Jane's reply as best...
        $thread->markBestReply($thread->addReply([
            'user_id' => $jane->id,
            'body' => 'Here is a reply.',
        ]));

        // Then Jane should receive the appropriate reputation points.
        $this->assertEquals($this->points['reply_posted'] + $this->points['best_reply_awarded'], $jane->fresh()->reputation);

        // But, if the owner of the thread decides to choose a different best reply, written by John.
        $john = create(User::class);

        $thread->markBestReply($thread->addReply([
            'user_id' => $john->id,
            'body' => 'Here is a better reply.',
        ]));

        // Then, Jane's reputation should be stripped of those "best reply" points.
        $this->assertEquals($this->points['reply_posted'], $jane->fresh()->reputation);

        // And those points should now be reflected on the account of the new best reply owner.
        $total = $this->points['reply_posted'] + $this->points['best_reply_awarded'];
        $this->assertEquals($total, $john->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserGainsPointsWhenWhenTheirReplyIsFavorited()
    {
        // Given we have a signed in user, John.
        $this->signIn($john = create(User::class));

        // And also Jane...
        $jane = create(User::class);

        // If Jane adds a new reply to a thread...
        $reply = create(Thread::class)->addReply([
            'user_id' => $jane->id,
            'body' => 'Some reply',
        ]);

        // And John favorites that reply.
        $this->post(route('replies.favorite', $reply));

        // Then, Jane's reputation should grow, accordingly.
        $this->assertEquals(
            $this->points['reply_posted'] + $this->points['reply_favorited'],
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
        $this->signIn($john = create(User::class));

        // And also Jane...
        $jane = create(User::class);

        // If Jane adds a new reply to a thread...
        $reply = create(Reply::class, ['user_id' => $jane]);

        // And John favorites that reply.
        $this->post(route('replies.favorite', $reply));

        // Then, Jane's reputation should grow, accordingly.
        $this->assertEquals(
            $this->points['reply_posted'] + $this->points['reply_favorited'],
            $jane->fresh()->reputation
        );

        // But, if John then unfavorites that reply...
        $this->delete(route('replies.unfavorite', $reply));

        // Then, Jane's reputation should be reduced, accordingly.
        $this->assertEquals($this->points['reply_posted'], $jane->fresh()->reputation);

        // While John's should remain unaffected.
        $this->assertEquals(0, $john->reputation);
    }
}
