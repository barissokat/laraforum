<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    /**
     *
     * @return void
     */
    public function testAThreadHasAPath()
    {
        $thread = create(Thread::class);

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /**
     *
     * @return void
     */
    public function testAThreadHasAOwner()
    {
        $this->assertInstanceOf(User::class, $this->thread->owner);
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanAddAReply()
    {
        $this->thread->addReply([
            'user_id' => 1,
            'body' => 'Foobar',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     *
     * @return void
     */
    public function testAThreadNotifiesAllRegisteredSubscribersWhenAReplyIsAdded()
    {
        Notification::fake();

        $this->signIn();

        $this->thread->subscribe()->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Foobar',
        ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);

    }

    /**
     *
     * @return void
     */
    public function testAThreadHasReplies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

        /**
     *
     * @return void
     */
    public function testAThreadCanHaveABestReply()
    {
        $reply = $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1,
        ]);

        $this->thread->markBestReply($reply);

        $this->assertEquals($reply->id, $this->thread->bestReply->id);
    }

    /**
     *
     * @return void
     */
    public function testAThreadBelongsToAChannel()
    {
        $thread = create(Thread::class);

        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanBeSubscribedTo()
    {
        $thread = create(Thread::class);

        $thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanBeUnsubscribedFrom()
    {
        $thread = create(Thread::class);

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }

    /**
     *
     * @return void
     */
    public function testItKnowsIfTheAuthenticatedUserIsSubscribedToIt()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanCheckIfTheAuthenticatedUserHasReadAllReplies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        tap(auth()->user(), fn($user) =>

            [
                $this->assertTrue($thread->hasUpdatesFor($user)),

                $user->read($thread),

                $this->assertFalse($thread->hasUpdatesFor($user)),
            ]
        );
    }

    /**
     *
     * @return void
     */
    public function testAThreadsBodyIsSanitizedAutomatically()
    {
        $thread = make(Thread::class, [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals('<p>This is okay.</p>', $thread->body);
    }
}
