<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Threads index test
     * 
     * @test
     *
     * @return void
     */
    public function aUserCanViewAllThreads()
    {
        $thread = factory('App\Thread')->create();

        $response = $this->get('/threads');
        // $response->assertStatus(200);
        $response->assertSee($thread->title);
        
        $response = $this->get('/threads/' . $thread->id);
        $response->assertSee($thread->title);
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
        $thread = factory('App\Thread')->create();
        
        $response = $this->get('/threads/' . $thread->id);
        $response->assertSee($thread->title);
    }
}
