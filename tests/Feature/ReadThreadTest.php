<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReadThreadTest extends TestCase
{
    use DatabaseMigrations;

    // protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
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
        $channel = create('App\Channel');

        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsByAnyUsername()
    {
        $this->signIn(create('App\User', ['name' => 'Baris']));


        $threadByBaris = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByBaris = create('App\Thread');

        $this->get('threads?by=Baris')
            ->assertSee($threadByBaris->title)
            ->assertDontSee($threadNotByBaris->title);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFilterThreadsByPopularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create('App\Thread');
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
        $thread = create('App\Thread');
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
        $thread = create('App\Thread');
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
        $thread = create('App\Thread');

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(2, $thread->fresh()->visits);
    }
}
