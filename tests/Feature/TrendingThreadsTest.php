<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Redis::del('trending_threads');
    }

    /**
     * @test
     *
     * @return void
     */
    public function itIncrementsAThreadsScoreEachTimeItIsRead()
    {
        $this->assertEmpty(Redis::zrevrange('trending_threads', 0, -1));

        $thread = create('App\Thread');

        $this->call('GET', $thread->path());

        $trending = Redis::zrevrange('trending_threads', 0, -1);

        $this->assertCount(1, $trending);

        $this->assertEquals($thread->title, json_decode($trending[0])->title);
    }
}
