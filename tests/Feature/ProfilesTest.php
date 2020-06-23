<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAUserHasAProfile()
    {
        $user = create(User::class);

        $response = $this->getJson("/profiles/{$user->username}")->json();

        $this->assertEquals($response['profileUser']['name'], $user->name);
    }

    /**
     *
     * @return void
     */
    public function testProfilesDisplayAllThreadsCreatedByTheAssociatedUser()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get(route('profiles.show', auth()->user()->username))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
