<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testAdministratorsCanPinThreads()
    {
        $this->signInAdmin();

        $thread = create('App\Thread');

        $this->post(route('pinned-threads.store', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /**
     * @return void
     */
    public function testAdministratorsCanUnpinThreads()
    {
        $this->signInAdmin();

        $thread = create('App\Thread', ['pinned' => true]);

        $this->post(route('pinned-threads.destroy', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /**
     * @return void
     */
    public function testPinnedThreadsAreListedFirst()
    {
        $this->signInAdmin();

        $threads = create('App\Thread', [], 3);
        $ids = $threads->pluck('id');

        $this->getJson('threads')->assertJson([
            'data' => [
                ['id' => $ids[0]],
                ['id' => $ids[1]],
                ['id' => $ids[2]],
            ],
        ]);

        $this->post(route('pinned-threads.store', $pinned = $threads->last()));

        $this->getJson('/threads')->assertJson([
            'data' => [
                ['id' => $pinned->id],
                ['id' => $ids[0]],
                ['id' => $ids[1]]
            ]
        ]);
    }
}
