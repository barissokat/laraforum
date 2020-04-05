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
}
