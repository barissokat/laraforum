<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testOnlyMembersCanAddAvatars()
    {
        $this->json('POST', 'api/users/1/avatar')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     *
     * @return void
     */
    public function testAValidAvatarMustBeProvided()
    {
        $this->signIn();

        $this->json('POST', route('avatar.store', auth()->id()), [
            'avatar' => 'not-an-image',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     * @return void
     */
    public function testAUserMayAddAnAvatarToTheirProfile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST', route('avatar.store', auth()->id()), [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertEquals(asset(Storage::url(('avatars/' . $file->hashName()))), auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
