<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function guestMayNotCreateThreads()
    {
        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /**
     * @test
     *
     * @return void
     */
    public function anAuthenticatedUserCanCreateNewForumThreads()
    {
        // Given we have a user
        // And that user is authenticated
        $this->signIn();

        // And we have a thread created by that user
        $thread = create('App\Thread');

        // And once we hit the endpoint to create a new thread
        $this->post('/threads', $thread->toArray());

        // And when we visit the thread page
        $response = $this->get($thread->path());

        // Then we should see the new thread's title and body
        $response->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
