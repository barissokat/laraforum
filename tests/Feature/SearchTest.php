<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAUserCanSearchThreads()
    {
        if (!config('scout.algolia.id')) {
            $this->markTestSkipped("Algolia is not configured.");
        }

        config(['scout.driver' => 'algolia']);

        create(Thread::class, [], 2);
        create(Thread::class, ['body' => 'A thread with the foobar term.'], 2);

        do {
            // Account for latency.
            sleep(.25);

            $results = $this->getJson('/threads/search?q=foobar')->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        // Clean up.
        Thread::latest()->take(4)->unsearchable();
    }
}
