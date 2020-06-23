<?php

namespace Tests\Feature;

use App\Activity;
use App\Channel;
use App\Reply;
use App\Rules\Recaptcha;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Component\HttpFoundation\Response;
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
        $user = factory(User::class)->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make(Thread::class);

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
        if (Recaptcha::isInTestMode()) {
            $this->markTestSkipped("Recaptcha is in test mode.");
        }

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
        create(Channel::class, [], 2);

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

        $thread = create(Thread::class, ['title' => 'Foo Title']);

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

        $thread = create(Thread::class, ['title' => 'Some Title 23']);

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

        $thread = create(Thread::class);

        $this->delete(route('threads.destroy', [$thread->channel->slug, $thread]))
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete(route('threads.destroy', [$thread->channel->slug, $thread]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     *
     * @return void
     */
    public function testAuthorizedUsersCanDeleteThreads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', route('threads.destroy', [$thread->channel->slug, $thread]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

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
    public function testANewThreadCannotBeCreatedInAnArchivedChannel()
    {
        $channel = create(Channel::class, ['archived' => true]);

        $this->publishThread(['channel_id' => $channel->id])
            ->assertSessionHasErrors('channel_id');

        $this->assertCount(0, $channel->threads);
    }

    /**
     *
     * @return void
     */
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make(Thread::class, $overrides);

        return $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}
