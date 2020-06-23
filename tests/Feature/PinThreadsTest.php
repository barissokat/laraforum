<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testRegularUsersCannotPinThreads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->post(route('pinned-threads.store', $thread))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /**
     * @return void
     */
    public function testRegularUsersCannotUnpinThreads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id(), 'pinned' => true]);

        $this->delete(route('pinned-threads.destroy', $thread))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /**
     * @return void
     */
    public function testAdministratorsCanPinThreads()
    {
        $this->signInAdmin();

        $thread = create(Thread::class);

        $this->post(route('pinned-threads.store', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /**
     * @return void
     */
    public function testAdministratorsCanUnpinThreads()
    {
        $this->signInAdmin();

        $thread = create(Thread::class, ['pinned' => true]);

        $this->post(route('pinned-threads.destroy', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was unpinned.');
    }

    /**
     * @return void
     */
    public function testPinnedThreadsAreListedFirst()
    {
        $this->signInAdmin();

        $threads = create(Thread::class, [], 3);
        $ids = $threads->pluck('id');

        $response_data = $this->getJson('/threads')->decodeResponseJson()['data'];
        $this->assertEquals($ids[0], $response_data[0]['id']);
        $this->assertEquals($ids[1], $response_data[1]['id']);
        $this->assertEquals($ids[2], $response_data[2]['id']);

        $this->post(route('pinned-threads.store', $pinned = $threads->last()));

        $response_data = $this->getJson('/threads')->decodeResponseJson()['data'];
        $this->assertEquals($pinned->id, $response_data[0]['id']);
        $this->assertEquals($ids[0], $response_data[1]['id']);
        $this->assertEquals($ids[1], $response_data[2]['id']);
    }
}
