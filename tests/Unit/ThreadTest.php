<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
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

        $this->thread = create('App\Thread');
    }

    /**
     *
     * @return void
     */
    public function testAThreadHasAPath()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /**
     *
     * @return void
     */
    public function testAThreadHasAOwner()
    {
        $this->assertInstanceOf('App\User', $this->thread->owner);
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
            'user_id' => 1,
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
    public function testAThreadBelongsToAChannel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanBeSubscribedTo()
    {
        $thread = create('App\Thread');

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
        $thread = create('App\Thread');

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
        $thread = create('App\Thread');

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

        $thread = create('App\Thread');

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
        $thread = make('App\Thread', [
            'body' => '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals('<p>This is okay.</p>', $thread->body);
    }
}
