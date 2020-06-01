<?php

namespace Tests\Feature;

use App\Mentions;
use App\Reply;
use App\Thread;
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
        $john = create('App\User', ['name' => 'JohnDoe']);

        $this->signIn($john);

        // And we also have a user, JaneDoe.
        $jane = create('App\User', ['name' => 'JaneDoe']);

        // And JohnDoe create new thread and mentions @JaneDoe.
        $thread = make('App\Thread', [
            'body' => 'Hey @JaneDoe check this out.',
        ]);

        $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        // Then @JaneDoe should receive a notification.
        $this->assertCount(1, $jane->notifications);
    }

    /**
     *
     * @return void
     */
    public function testMentionedUsersInAReplyAreNotified()
    {
        // Given we have a user, JohnDoe, who is signed in.
        $john = create('App\User', ['name' => 'JohnDoe']);

        $this->signIn($john);

        // And we also have a user, JaneDoe.
        $jane = create('App\User', ['name' => 'JaneDoe']);

        // And JohnDoe create new thread and mentions in Reply @JaneDoe.
        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => '@JaneDoe look at this. Also @FrankDoe"',
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        // Then @JaneDoe should receive a notification.
        $this->assertCount(1, $jane->notifications);
    }

    /**
     * @return void
     */
    public function testItFetchesAllMentionedUsersStartingWithTheGivenCharacters()
    {
        $this->withoutExceptionHandling();
        create('App\User', ['name' => 'JohnDoe']);
        create('App\User', ['name' => 'JohnDoe2']);
        create('App\User', ['name' => 'JaneDoe']);

        $response = $this->json('GET', '/api/users', ['name' => 'John']);

        $this->assertCount(2, $response->json());
    }


    /**
     * @return void
     */
    function testItCanDetectAllMentionedUsersInTheBody()
    {
        $thread = new Thread([
            'body' => '@JohnDoe wants to talk to @JaneDoe'
        ]);

        $reply = new Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JohnDoe', 'JaneDoe'], Mentions::mentionedUsers($thread->body));
        $this->assertEquals(['JaneDoe', 'JohnDoe'], Mentions::mentionedUsers($reply->body));
    }
}
