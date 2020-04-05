<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanMakeAStringPath()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadHasAOwner()
    {
        $this->assertInstanceOf('App\User', $this->thread->owner);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadCanAddAReply()
    {
        $this->thread->addReply([
            'user_id' => 1,
            'body' => 'Foobar',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadHasReplies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /**
     * @test
     *
     * @return void
     */
    public function aThreadBelongsToAChannel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }
}
