<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);
uses(WithFaker::class);

beforeEach(function () {
    Artisan::call('passport:install');
});

test('User tries to register without data', function () {
    $this->post('/api/register', [])
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['name' => ['The name field is required.']])
         ->assertJsonFragment(['email' => ['The email field is required.']])
         ->assertJsonFragment(['password' => ['The password field is required.']]);
});

test('User tries to register with invalid data', function () {
    // validation rules in App\Http\Requests\RegisterRequest
    $input = ['name'     => '1',
              'email'    => 'e',
              'password' => '1'];

    $this->post('/api/register', $input)
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['name' => ['The name must be at least 3 characters.']])
         ->assertJsonFragment(['email' => ['The email must be a valid email address.']])
         ->assertJsonFragment(['password' => ['The password confirmation does not match.',
                                              'The password must be at least 6 characters.']]);

    $input = ['name'                  => 'name',
              'email'                 => 'email@mail.com',
              'password'              => '1',
              'password_confirmation' => '1'];

    $this->post('/api/register', $input)
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['password' => ['The password must be at least 6 characters.']]);

    $input = ['name'                  => 'name',
              'email'                 => 'email@mail.com',
              'password'              => '123456',
              'password_confirmation' => '1'];

    $this->post('/api/register', $input)
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['password' => ['The password confirmation does not match.']]);
});

test('User tries to register with an already registered email', function () {
    $user = User::factory()->create();

    $input = ['name'                  => 'name',
              'email'                 => $user->email,
              'password'              => '123456',
              'password_confirmation' => '123456'];

    $this->post('/api/register', $input)
         ->assertStatus(422)
         ->assertJsonFragment(['email' => ['The email has already been taken.']]);
});

test('User is able to register', function () {
    // validation rules in App\Http\Requests\RegisterRequest
    $name = $this->faker()->name;
    $input = ['name'                  => $name,
              'email'                 => 'email@mail.com',
              'password'              => '123456',
              'password_confirmation' => '123456'];

    $result = $this->post('/api/register', $input)
                   ->assertCreated()
                   ->assertJsonFragment(['success' => true])
                   ->assertJsonFragment(['message' => 'User created'])
                   ->assertJsonFragment(['name' => $name])
                   ->assertJsonStructure(['data' => ['access_token', 'name']]);

    // validate the registered user is not an admin
    $registeredUser = User::find($result['data']['id']);
    $this->assertFalse($registeredUser->is_admin);
});

test('User tries to register as admin and fails', function () {
    // validation rules in App\Http\Requests\RegisterRequest
    $name = $this->faker()->name;
    $input = ['name'                  => $name,
              'email'                 => 'email@mail.com',
              'is_admin'              => true,
              'password'              => '123456',
              'password_confirmation' => '123456'];

    $result = $this->post('/api/register', $input)
                   ->assertCreated()
                   ->assertJsonFragment(['success' => true])
                   ->assertJsonFragment(['message' => 'User created']);

    // validate the registered user is not an admin
    $registeredUser = User::find($result['data']['id']);
    $this->assertFalse($registeredUser->is_admin);
});

test('User tries to login with incorrect data', function () {
    $password = 'secret';
    $user = User::factory()->create(['password' => bcrypt($password)]);

    $this->post('/api/login', [])
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['email' => ['The email field is required.']])
         ->assertJsonFragment(['password' => ['The password field is required.']]);

    $input = ['email'    => $user->email,
              'password' => 'fake'];

    $this->post('/api/login', $input)
         ->assertUnauthorized()
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['message' => 'Incorrect user or password']);
});

test('User is able to login', function () {
    $password = 'secret';
    $user = User::factory()->create(['password' => bcrypt($password)]);

    $input = ['email'    => $user->email,
              'password' => $password];

    $this->post('/api/login', $input)
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['message' => 'Successful login'])
         ->assertJsonStructure(['data' => ['access_token', 'expires_at', 'token_type']]);
});

test('User is able to logout', function () {
    $password = 'secret';
    $user = User::factory()->create(['password' => bcrypt($password)]);

    $input = ['email'    => $user->email,
              'password' => $password];

    $response = $this->post('/api/login', $input);

    $this->withHeaders(['Authorization' => 'Bearer ' . $response['data']['access_token'],
                        'Accept'        => 'application/json'])
         ->post('/api/logout')
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['message' => 'Successfully logged out'])
         ->assertJsonStructure(['data' => []]);
});