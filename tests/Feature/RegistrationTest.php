<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function aConfirmationEmailIsSentUponRegistration()
    {
        Mail::fake();

        $user = create('App\User', ['email_verified_at' => null]);

        event(new Registered($user));

        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function userCanFullyConfirmTheirEmailAddresses()
    {
        $this->post('/register', [
            'name' => 'Baris',
            'email' => 'baris@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $user = User::whereName('Baris')->first();

        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->hasVerifiedEmail());
    }
}
