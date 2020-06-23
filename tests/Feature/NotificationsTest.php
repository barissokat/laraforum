<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /**
     *
     * @return void
     */
    public function testANotificationsIsPreparedWhenASubscribedThreadReceivesANewReplyThatIsNotByTheCurrentUser()
    {
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply here',
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply here',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /**
     *
     * @return void
     */
    public function testAUserCanFetchTheirUnreadNotifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson(route('notifications.index', auth()->user()->name))->json()
        );
    }

    /**
     *
     * @return void
     */
    public function testAUserCanMarkANotificationAsRead()
    {
        create(DatabaseNotification::class);

        tap(auth()->user(), fn($user) =>
            [
                $this->assertCount(1, $user->unreadNotifications),

                $this->delete(route('notifications.destroy', [$user->username, $user->unreadNotifications->first()->id])),

                $this->assertCount(0, $user->fresh()->unreadNotifications),
            ]
        );
    }
}
