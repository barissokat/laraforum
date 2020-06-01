<?php

namespace Tests\Feature;

use App\Activity;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    /**
     *
     * @return void
     */
    public function testGuestMayNotCreateThreads()
    {
        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /**
     *
     * @return void
     */
    public function testNewUsersMustFirstConfirmTheirEmailAddressBeforeCreatingThreads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads.store'), $thread->toArray())
            ->assertRedirect('/email/verify');
        // ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    /**
     *
     * @return void
     */
    public function testAUserCanCreateNewForumThreads()
    {
        $response = $this->publishThread(['title' => 'Some Title', 'body' => 'Some body.']);

        $this->get($response->headers->get('Location'))
            ->assertSee('Some Title')
            ->assertSee('Some body.');
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresATitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresABody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresRecaptchaVerification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread()
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresAValidChannel()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresAUniqueSlug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /**
     *
     * @return void
     */
    public function testAThreadWithATitleThatEndsInANumberShouldGenerateTheProperSlug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some Title 23']);

        $thread = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("some-title-23-{$thread['id']}", $thread['slug']);
    }

    /**
     *
     * @return void
     */
    public function testUnauthorizedUsersMayNotDeleteThreads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete(route('threads.destroy', [$thread->channel->slug, $thread]))
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete(route('threads.destroy', [$thread->channel->slug, $thread]))
            ->assertStatus(403);
    }

    /**
     *
     * @return void
     */
    public function testAuthorizedUsersCanDeleteThreads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', route('threads.destroy', [$thread->channel->slug, $thread]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id,
            'type' => 'created_thread',
        ]);
        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'type' => 'created_reply',
        ]);

        $this->assertEquals(0, Activity::count());
    }

    /**
     *
     * @return void
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
