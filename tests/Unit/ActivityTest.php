<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testItRecordsActivityWhenAThreadIsCreated()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'type' => 'created_thread',
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread',
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /**
     *
     * @return void
     */
    public function testItRecordsActivityWhenAReplyIsCreated()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->assertEquals(2, Activity::count());
    }

    /**
     *
     * @return void
     */
    public function testItFetchesAFeedForAnyUser()
    {
        $this->signIn();

        create('App\Thread', ['user_id' => auth()->id()], 2);

        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user());

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('d-m-Y')
        ));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('d-m-Y')
        ));
    }
}
