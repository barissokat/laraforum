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

        $this->thread = factory('App\Thread')->create();
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
        $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);

        $response = $this->get($this->thread->path())
            ->assertSee($reply->body);
    }
}
