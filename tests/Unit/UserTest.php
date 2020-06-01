<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testAUserCanFetchTheirMostRecentReply()
    {
        $user = create('App\User');

        $reply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanDetermineTheirAvatarPath()
    {
        $user = create('App\User');

        $this->assertEquals(asset('storage/avatars/default.jpg'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals(asset('storage/avatars/me.jpg'), $user->avatar_path);
    }
}
