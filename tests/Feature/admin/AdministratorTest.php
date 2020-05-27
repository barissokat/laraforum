<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /**
     *
     * @return void
     */
    public function testAnAdministratorCanAccessTheAdministratorSection()
    {
        $administrator = factory('App\User')->states('administrator')->create();

        $this->actingAs($administrator)
            ->get('/admin')
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     * @return void
     */
    public function testNonAdministratorCannotAccessTheAdministratorSection()
    {
        $regularUser = factory('App\User')->create();

        $this->actingAs($regularUser)
            ->get('/admin')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
