<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testUsersCanRegisterAnAccount()
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertRedirect('/threads');

        $this->assertTrue(Auth::check());

        $this->assertCount(1, User::all());

        tap(User::first(), function ($user) {
            $this->assertEquals('John Doe', $user->name);
            $this->assertEquals('johndoe', $user->username);
            $this->assertEquals('johndoe@example.com', $user->email);
            $this->assertTrue(Hash::check('12345678', $user->password));
        });
    }

    /**
     *
     * @return void
     */
    public function testAConfirmationEmailIsSentUponRegistration()
    {
        Event::fake();

        $this->post(route('register'), $this->validParams());

        Event::assertDispatched(Registered::class);
    }

    /**
     *
     * @return void
     */
    public function testNameIsOptional()
    {
        $response = $this->post(route('register'), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect('/threads');
        $this->assertTrue(Auth::check());
        $this->assertCount(1, User::all());
    }

    /**
     *
     * @return void
     */
    public function testNameCannotExceed255Chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'name' => str_repeat('a', 256),
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('name');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testUsernameIsRequired()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'username' => '',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('username');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testUsernameIsUrlSafe()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'username' => 'spaces and symbols!',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('username');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testUsernameCannotExceed255Chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'username' => str_repeat('a', 256),
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('username');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testUsernameIsUnique()
    {
        create(User::class, ['username' => 'john']);
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'username' => 'john',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('username');
        $this->assertFalse(Auth::check());
        $this->assertCount(1, User::all());
    }

    /**
     *
     * @return void
     */
    public function testEmailIsRequired()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'email' => '',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testEmailIsValid()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'email' => 'not-an-email-address',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testEmailCannotExceed255Chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'email' => substr(str_repeat('a', 256) . '@example.com', -256),
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testEmailIsUnique()
    {
        create(User::class, ['email' => 'johndoe@example.com']);
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'email' => 'johndoe@example.com',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
        $this->assertCount(1, User::all());
    }

    /**
     *
     * @return void
     */
    public function testPasswordIsRequired()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'password' => '',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testPasswordMustBeConfirmed()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'password' => 'foo',
            'password_confirmation' => 'bar',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    /**
     *
     * @return void
     */
    public function testPasswordMustBe6Chars()
    {
        $this->withExceptionHandling();
        $this->from(route('register'));

        $response = $this->post(route('register'), $this->validParams([
            'password' => 'foo',
            'password_confirmation' => 'foo',
        ]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
        $this->assertCount(0, User::all());
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ], $overrides);
    }
}
