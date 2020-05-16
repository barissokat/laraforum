<?php

namespace Tests\Feature;

use App\Activity;
use App\Thread;
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
    public function authenticatedUsersMustFirstConfirmTheirEmailAddressBeforeCreatingThreads()
    {
        $user = create('App\User', ['email_verified_at' => null]);

        $this->withExceptionHandling()->signIn($user);

        $thread = make('App\Thread');

        $this->post('/threads', $thread->toArray())
            ->assertRedirect('/email/verify');

        // ->assertSessionHas('flash', 'You must first confirm your email address.');
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
    public function aThreadRequiresAUniqueSlug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson(route('threads.index'), $thread->toArray())->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadWithATitleThatEndsInANumberShouldGenerateTheProperSlug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some Title 23']);

        $thread = $this->postJson(route('threads.index'), $thread->toArray())->json();

        $this->assertEquals("some-title-23-{$thread['id']}", $thread['slug']);
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

        $this->delete(route('threads.delete', [$thread->channel->slug, $thread]))
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete(route('threads.delete', [$thread->channel->slug, $thread]))
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

        $response = $this->json('DELETE', route('threads.delete', [$thread->channel->slug, $thread]));

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
