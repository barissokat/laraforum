<?php

namespace Tests\Feature;

use App\Reputation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyCreateAThread()
    {
        $thread = create('App\Thread');

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheyDeleteAThread()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::THREAD_PUBLISHED, $thread->owner->reputation);


        $this->delete($thread->path());
        $this->assertEquals(0, $thread->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyReplyToAThread()
    {
        $thread = create('App\Thread');

        $reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.',
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserLosesPointsWhenTheyDeleteAReply()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

        $this->delete(route('replies.destroy', $reply));

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /**
     *
     * @return void
     */
    public function testAUserEarnsPointsWhenTheyReplyIsMarkedAsBest()
    {
        $thread = create('App\Thread');

        $reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.',
        ]);

        $total  = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;

        $thread->markBestReply($reply);

        $this->assertEquals($total , $reply->owner->reputation);
    }

    /**
     *
     * @return void
     */
     public function testAUserEarnsPointsWhenWhenTheirReplyIsFavorited()
     {
         $this->signIn();

         $thread = create('App\Thread');

         $reply = $thread->addReply([
             'user_id' => auth()->id(),
             'body' => 'Some reply'
         ]);

         $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;

         $this->post(route('replies.favorite', $reply));

         $this->assertEquals($total, $reply->owner->fresh()->reputation);
     }

     /**
      *
      * @return void
      */
     public function testAUserLosesPointsWhenTheirReplyIsUnfavorited()
     {
         $this->signIn();

         $reply = create('App\Reply', ['user_id' => auth()->id()]);

         $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;

         $this->post(route('replies.favorite', $reply));

         $this->assertEquals($total, $reply->owner->fresh()->reputation);

         $this->delete(route('replies.unfavorite', $reply));

         $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);
     }
}
