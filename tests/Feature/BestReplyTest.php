<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
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

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

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

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->signIn(create(User::class));

        $this->postJson(route('best-replies.store', [$replies[1]->id]))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /**
     *
     * @return void
     */
    public function testIfABestReplyIsDeletedThenTheThreadIsProperlyUpdatedToReflectThat()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
