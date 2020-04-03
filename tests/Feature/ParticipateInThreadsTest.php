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
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withoutExceptionHandling();

        $this->post('/threads/1/replies', []);
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
