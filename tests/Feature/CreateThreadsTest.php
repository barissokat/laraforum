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
        $thread = factory('App\Thread')->make();

        // And a guest posts a new thread to the endpoint
        $this->post('/threads', $thread->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function anAuthenticatedUserCanCreateNewForumThreads()
    {
        // Given we have a user
        $user = factory('App\User')->create();

        // And that user is authenticated
        $this->actingAs($user);

        // And we have a thread created by that user
        $thread = factory('App\Thread')->make();

        // And once we hit the endpoint to create a new thread
        $this->post('/threads', $thread->toArray());

        // And when we visit the thread page
        $response = $this->get($thread->path());

        // Then we should see the new thread's title and body
        $response->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
