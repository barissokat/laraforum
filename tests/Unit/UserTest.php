<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function aUserCanFetchTheirMostRecentReply()
    {
        $user = create('App\User');

        $reply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }
}
