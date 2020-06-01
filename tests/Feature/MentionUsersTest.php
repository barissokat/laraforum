<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function testMentionedUsersInAReplyAreNotified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        $jane = create('App\User', ['name' => 'JaneDoe']);

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => '@JaneDoe look at this. Also @FrankDoe"',
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /**
     * @test
     *
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
}
