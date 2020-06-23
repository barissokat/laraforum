<?php

namespace Tests\Feature;

use App\Mentions;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testMentionedUsersInAThreadAreNotified()
    {
        // Given we have a user, JohnDoe, who is signed in.
        $john = create(User::class, ['username' => 'JohnDoe']);

        $this->signIn($john);

        // And we also have a user, JaneDoe.
        $jane = create(User::class, ['username' => 'JaneDoe']);

        // And JohnDoe create a new thread and mentions @JaneDoe.
        $thread = make(Thread::class, [
            'body' => 'Hey @JaneDoe check this out.',
        ]);

        $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        // Then @JaneDoe should receive a notification.
        $this->assertCount(1, $jane->notifications);

        $this->assertEquals(
            "{$john->username} mentioned you in \"{$thread->title}\"",
            $jane->notifications->first()->data['message']
        );
    }

    /**
     *
     * @return void
     */
    public function testMentionedUsersInAReplyAreNotified()
    {
        // Given we have a user, JohnDoe, who is signed in.
        $john = create(User::class, ['username' => 'JohnDoe']);

        $this->signIn($john);

        // And we also have a user, JaneDoe.
        $jane = create(User::class, ['username' => 'JaneDoe']);

        // And JohnDoe create new thread and mentions in Reply @JaneDoe.
        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => '@JaneDoe look at this. Also @FrankDoe"',
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        // Then @JaneDoe should receive a notification.
        $this->assertCount(1, $jane->notifications);

        $this->assertEquals(
            "{$john->username} mentioned you in \"{$thread->title}\"",
            $jane->notifications->first()->data['message']
        );
    }

    /**
     * @return void
     */
    public function testItFetchesAllMentionedUsersStartingWithTheGivenCharacters()
    {
        $this->withoutExceptionHandling();
        create(User::class, ['username' => 'JohnDoe']);
        create(User::class, ['username' => 'JohnDoe2']);
        create(User::class, ['username' => 'JaneDoe']);

        $response = $this->json('GET', '/api/users', ['username' => 'John']);

        $this->assertCount(2, $response->json());
    }
}
