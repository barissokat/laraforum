<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ParticipateInThreadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testUnauthenticatedUsersMayNotAddReplies()
    {
        $this->withExceptionHandling();

        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /**
     *
     * @return void
     */
    public function testAnAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /**
     *
     * @return void
     */
    public function testAReplyRequiresABody()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     * @return void
     */
    public function testUnauthorizedUsersCannotDeleteReplies()
    {
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     *
     * @return void
     */
    public function testAuthorizedUsersCanDeleteReplies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /**
     *
     * @return void
     */
    public function testUnauthorizedUsersCannotUpdateReplies()
    {
        $reply = create('App\Reply');

        $this->patch(route('replies.update', $reply->id))
            ->assertRedirect('login');

        $this->signIn()
            ->patch(route('replies.update', $reply->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     *
     * @return void
     */
    public function testAuthorizedUsersCanUpdateReplies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $updatedReply = "You been changed, fool.";
        $this->patch(route('replies.update', $reply->id), ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply,
        ]);
    }

    /**
     *
     * @return void
     */
    public function testRepliesThatContainSpamMayNotBeCreated()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support',
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     * @return void
     */
    public function testUsersMayOnlyReplyAMaximumOfOncePerMinute()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(Response::HTTP_CREATED);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
