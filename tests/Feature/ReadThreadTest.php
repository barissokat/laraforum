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
     * @test
     *
     * @return void
     */
    public function aUserCanViewAllThreads()
    {
        $response = $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanReadASingleThread()
    {
        $response = $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanFilterThreadsAccordingToAChannel()
    {
        $channel = create('App\Channel');

        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanFilterThreadsByAnyUsername()
    {
        $this->signIn(create('App\User', ['name' => 'Baris']));

        $threadByBaris = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByBaris = create('App\Thread', ['user_id' => 99]);

        $this->get('threads?by=Baris')
            ->assertSee($threadByBaris->title)
            ->assertDontSee($threadNotByBaris->title);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanFilterThreadsByPopularity()
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
     * @test
     *
     * @return void
     */
    public function aUserCanFilterThreadsByThoseThatAreUnanswered()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=all')->json();

        $this->assertCount(1, $response['data']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanRequestAllRepliesForAGivenThread()
    {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id], 2);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
