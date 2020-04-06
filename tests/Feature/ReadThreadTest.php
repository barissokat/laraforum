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
    public function aUserCanReadRepliesThatAreAssociatedWithAThread()
    {
        $reply = create('App\Reply', ['thread_id' => $this->thread->id]);

        $response = $this->get($this->thread->path())
            ->assertSee($reply->body);
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
}
