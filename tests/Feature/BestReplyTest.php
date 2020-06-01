<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    /**
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
     *
     * @return void
     */
    public function testOnlyTheThreadCreatorMayMarkAReplyAsBest()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->postJson(route('best-replies.store', [$replies[1]->id]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /**
     *
     * @return void
     */
    public function testIfABestReplyIsDeletedThenTheThreadIsProperlyUpdatedToReflectThat()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
