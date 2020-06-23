<?php

namespace Tests\Feature;

use App\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testGuestsCanNotFavoriteAnything()
    {
        $this->post('replies/1/favorites')
            ->assertRedirect('/login');
    }

    /**
     *
     * @return void
     */
    public function testAnAuthenticatedUserCanFavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorite', $reply->id));

        $this->assertCount(1, $reply->favorites);
    }

    /**
     *
     * @return void
     */
    public function testAnAuthenticatedUserCanUnfavoriteAnyReply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite();

        $this->delete(route('replies.unfavorite', $reply->id));

        $this->assertCount(0, $reply->favorites);
    }

    /**
     *
     * @return void
     */
    public function testAnAuthenticatedUserMayOnlyFavoriteAReplyOnce()
    {
        $this->signIn();

        $reply = create(Reply::class);

        try {
            $this->post(route('replies.favorite', $reply->id));
            $this->post(route('replies.favorite', $reply->id));
        } catch (\Throwable $th) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
