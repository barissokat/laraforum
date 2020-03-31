<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Reply has an owner test example.
     *
     * @test
     *
     * @return void
     */
    public function itHasAnOwner()
    {
        $reply = factory(\App\Reply::class)->create();
        
        $this->assertInstanceOf('App\User', $reply->owner);
    }
}
