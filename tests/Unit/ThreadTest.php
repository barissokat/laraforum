<?php

namespace Tests\Unit;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanMakeAStringPath()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadHasAOwner()
    {
        $this->assertInstanceOf('App\User', $this->thread->owner);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanAddAReply()
    {
        $this->thread->addReply([
            'user_id' => 1,
            'body' => 'Foobar',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadNotifiesAllRegisteredSubscribersWhenAReplyIsAdded()
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
     * @test
     *
     * @return void
     */
    public function aThreadHasReplies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadBelongsToAChannel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanBeSubscribedTo()
    {
        $thread = create('App\Thread');

        $thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanBeUnsubscribedFrom()
    {
        $thread = create('App\Thread');

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itKnowsIfTheAuthenticatedUserIsSubscribedToIt()
    {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanCheckIfTheAuthenticatedUserHasReadAllReplies()
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
     * @test
     *
     * @return void
     */
    public function aThreadRecordsEachVisit()
    {
        $thread = make('App\Thread', ['id' => 1]);

        $thread->resetVisits();

        $this->assertSame(0, $thread->visits);

        $thread->recordVisit();

        $this->assertEquals(1, $thread->visits);

        $thread->recordVisit();

        $this->assertEquals(2, $thread->visits);
    }
}
