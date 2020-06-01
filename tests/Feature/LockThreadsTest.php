<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testNonAdministratorsMayNotLockThreads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($thread->fresh()->locked);
    }

    /**
     *
     * @return void
     */
    public function testAdministratorsCanLockThreads()
    {
        $this->signInAdmin();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /**
     *
     * @return void
     */
    public function testAdministratorsCanUnlockThreads()
    {
        $this->signInAdmin();

        $thread = create('App\Thread', ['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /**
     *
     * @return void
     */
    public function testOnceLockedAThreadMayNotReceiveNewReplies()
    {
        $this->signIn();

        $thread = create('App\Thread', ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id(),
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
