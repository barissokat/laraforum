<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAThreadCreatorMayMarkAnyReplyAsTheBestReply()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', $replies[1]->id));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testOnlyTheThreadCreatorMayMarkAReplyAsBest()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->postJson(route('best-replies.store', $replies[1]->id))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }
}
