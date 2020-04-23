<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ManageThreadsTest extends TestCase
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
        $thread = make('App\Thread');

        // And once we hit the endpoint to create a new thread
        $response = $this->post('/threads', $thread->toArray());

        // And when we visit the thread page
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadRequiresATitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadRequiresABody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadRequiresAValidChannel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /**
     * @test
     *
     * @return void
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/threads', $thread->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function unauthorizedUsersMayNotDeleteThreads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);
    }

    /**
     * @test
     *
     * @return void
     */
    public function authorizedUsersCanDeleteThreads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id,
            'type' => 'created_thread',
        ]);
        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'type' => 'created_reply',
        ]);

        $this->assertEquals(0, Activity::count());
    }
}
