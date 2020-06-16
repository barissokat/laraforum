<?php

namespace Tests\Unit;

use App\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAChannelConsistsOfThreads()
    {
        $channel = create('App\Channel');
        $thread = create('App\Thread', ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }

    /**
     *
     * @return void
     */
    public function testAChannelCanBeArchived()
    {
        $channel = create('App\Channel');

        $this->assertFalse($channel->archived);

        $channel->archive();

        $this->assertTrue($channel->archived);
    }

    /**
     *
     * @return void
     */
    public function testArchivedChannelsAreExcludedByDefault()
    {
        $channel = create('App\Channel');
        create('App\Channel', ['archived' => true]);

        $this->assertEquals(1, Channel::count());
    }
}
