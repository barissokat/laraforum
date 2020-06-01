<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /**
     *
     * @return void
     */
    public function testAThreadRequiresATitleAndBodyToBeUpdated()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'Changed body',
        ])->assertSessionHasErrors('title');
    }

    /**
     *
     * @return void
     */
    public function testUnauthorizedUsersMayNotUpdateThreads()
    {
        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);

        $this->patch($thread->path(), [])->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     *
     * @return void
     */
    public function testAThreadCanBeUpdatedByItsCreator()
    {
        // $thread = create('App\Thread', ['user_id' => create('App\User')->id]);
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
            'body' => 'Changed body',
        ]);

        tap($thread->fresh(), function ($thread) {
            $this->assertEquals('Changed', $thread->title);
            $this->assertEquals('Changed body', $thread->body);
        });
    }
}
