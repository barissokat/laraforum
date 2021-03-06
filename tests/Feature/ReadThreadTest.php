<?php

namespace Tests\Feature;

use App\Channel;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadTest extends TestCase
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
    public function testAUserCanViewAllThreads()
    {
        $response = $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanReadASingleThread()
    {
        $response = $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsAccordingToAChannel()
    {
        $channel = create(Channel::class);

        $threadInChannel = create(Thread::class, ['channel_id' => $channel->id]);
        $threadNotInChannel = create(Thread::class);

        $this->get(route('channels.index', $channel->slug))
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsByAnyUsername()
    {
        $this->signIn(create(User::class, ['username' => 'JohnDoe']));

        $johnsThread = create(Thread::class, ['user_id' => auth()->id()]);
        $janesThread = create(Thread::class);

        $this->get('threads?by=JohnDoe')
            ->assertSee($johnsThread->title)
            ->assertDontSee($janesThread->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create(Thread::class);
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=all')->json();

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $thread = create(Thread::class);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=all')->json();

        $this->assertCount(1, $response['data']);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanRequestAllRepliesForAGivenThread()
    {
        $thread = create(Thread::class);
        create('App\Reply', ['thread_id' => $thread->id], 2);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     *
     * @return void
     */
    public function testWeRecordANewVisitEachTimeTheThreadIsRead()
    {
        $thread = create(Thread::class);

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(2, $thread->fresh()->visits);
    }
}
