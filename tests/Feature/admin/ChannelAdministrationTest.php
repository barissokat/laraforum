<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ChannelAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAnAdministratorCanAccessTheChannelAdministrationSection()
    {
        $this->signInAdmin()
            ->get(route('admin.channels.index'))
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     * @return void
     */
    public function testNonAdministratorCannotAccessTheChannelAdministrationSection()
    {
        $this->signIn()
            ->get(route('admin.channels.index'))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->signIn()
            ->get(route('admin.channels.create'))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     *
     * @return void
     */
    public function testAnAdministratorCanCreateAChannel()
    {
        $response = $this->createChannel([
            'name' => 'php',
            'description' => 'This is the channel for discussing all things PHP.',
        ]);

        $this->get($response->headers->get('Location'))
            ->assertSee('php')
            ->assertSee('This is the channel for discussing all things PHP.');
    }

    public function testAnAdministratorCanEditAnExistingChannel()
    {
        $this->signInAdmin();

        $channel = create('App\Channel');


        $this->patch(route('admin.channels.update', $channel), [
            'name' => 'Altered',
            'description' => 'Altered channel description',
            'archived' => true,
        ]);

        tap($channel->fresh(), function ($channel) {
            $this->assertEquals('Altered', $channel->name);
            $this->assertEquals('Altered channel description', $channel->description);
        });
    }

    /**
     *
     * @return void
     */
    public function testAnAdministratorCanMarkAnExistingChannelAsArchived()
    {
        $this->signInAdmin();

        $channel = create('App\Channel');

        $this->assertFalse($channel->archived);

        $this->patch(route('admin.channels.update', $channel), [
            'name' => 'Altered',
            'description' => 'Altered channel description',
            'archived' => true,
        ]);

        $this->assertTrue($channel->fresh()->archived);
    }

    /**
     *
     * @return void
     */
    public function testThePathToAChannelIsUnaffectedByItsArchivedStatus()
    {
        $thread = create('App\Thread');

        $path = $thread->path();

        $thread->channel->archive();

        $this->assertEquals($path, $thread->fresh()->path());
    }

    /**
     *
     * @return void
     */
    public function testAChannelRequiresAName()
    {
        $this->createChannel(['name' => null])
            ->assertSessionHasErrors('name');
    }

    /**
     *
     * @return void
     */
    public function testAChannelRequiresADescription()
    {
        $this->createChannel(['description' => null])
            ->assertSessionHasErrors('description');
    }

    protected function createChannel($overrides = [])
    {
        $this->signInAdmin();

        $channel = make('App\Channel', $overrides);

        return $this->post(route('admin.channels.store'), $channel->toArray());
    }

}
