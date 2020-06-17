<?php

namespace Tests\Unit;

use App\Channel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        EloquentCollection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items));

            $this->zip($items)->each(function ($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
    }

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

    /**
     *
     * @return void
     */
    public function testChannelsAreSortedAlphabeticallyByDefault()
    {
        $channelA = create('App\Channel', ['name' => 'PHP']);
        $channelB = create('App\Channel', ['name' => 'Basic']);
        $channelC = create('App\Channel', ['name' => 'Zsh']);

        $channels = Channel::all();

        $channels->assertEquals([
            $channelB,
            $channelA,
            $channelC,
        ]);
    }
}
