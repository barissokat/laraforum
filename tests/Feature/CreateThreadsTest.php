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
        // We should expect an authenticated error exception
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withoutExceptionHandling();

        // Given we have a thread
        $thread = make('App\Thread');

        // And a guest posts a new thread to the endpoint
        $this->post('/threads', $thread->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function guestCannotSeeTheCreateThreadPage()
    {
        $this->get('/threads/create')
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
        $thread = make('App\Thread');

        // And once we hit the endpoint to create a new thread
        $this->post('/threads', $thread->toArray());

        // And when we visit the thread page
        $response = $this->get($thread->path());

        // Then we should see the new thread's title and body
        $response->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
