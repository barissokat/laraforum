<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReadThreadTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = factory(\App\Thread::class)->create();
    }

    /**
     * Threads index test
     *
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
     * Threads show test
     *
     * @test
     *
     * @return void
     */
    public function aUserCanReadASingleThread()
    {
        $response = $this->get('/threads/' . $this->thread->id)
            ->assertSee($this->thread->title);
    }

    /**
     * Replies show test
     *
     * @test
     *
     * @return void
     */
    public function aUserCanReadRepliesThatAreAssociatedWithAThread()
    {
        $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);

        $response = $this->get('/threads/' . $this->thread->id)
            ->assertSee($reply->body);
    }
}
