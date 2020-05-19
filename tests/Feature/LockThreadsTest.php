<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @return void
     */
    public function testNonAdministratorsMayNotLockThreads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertFalse(!!$thread->fresh()->locked);
    }

    /**
     *
     * @return void
     */
    public function testAdministratorsCanLockThreads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue(!!$thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /**
     *
     * @return void
     */
    public function testOnceLockedAThreadMayNotReceiveNewReplies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id(),
        ])->assertStatus(422);
    }
}
