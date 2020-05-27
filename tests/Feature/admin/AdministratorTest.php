<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAnAdministratorCanAccessTheAdministratorSection()
    {
        $this->signInAdmin()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     * @return void
     */
    public function testNonAdministratorCannotAccessTheAdministratorSection()
    {
        $this->signIn()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
