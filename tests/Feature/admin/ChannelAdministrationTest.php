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
    public function testAnAdministratorCanCreateaChannel()
    {
        $response = $this->createChannel([
            'name' => 'php',
            'description' => 'This is the channel for discussing all things PHP.',
        ]);

        $this->get($response->headers->get('Location'))
            ->assertSee('php')
            ->assertSee('This is the channel for discussing all things PHP.');
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
