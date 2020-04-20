<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function unauthenticatedUsersMayNotAddReplies()
    {
        $this->withExceptionHandling();

        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /**
     * @test
     *
     * @return void
     */
    public function anAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->be($user = create('App\User'));

        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->get($thread->path())->assertSee($reply->body);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aReplyRequiresABody()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /**
     * @test
     *
     * @return void
     */
    public function unauthorizedUsersCannotDeleteReplies()
    {
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /**
     * @test
     *
     * @return void
     */
    public function authorizedUsersCanDeleteReplies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function unauthorizedUsersCannotUpdateReplies()
    {
        $reply = create('App\Reply');

        $updatedReply = "You been changed, fool.";
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply])
            ->assertRedirect('/login');

        $this->signIn()
            ->patch("/replies/{$reply->id}", ['body' => $updatedReply])
            ->assertStatus(403);
    }

    /**
     * @test
     *
     * @return void
     */
    public function authorizedUsersCanUpdateReplies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $updatedReply = "You been changed, fool.";
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply,
        ]);
    }
}
