<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class);
uses(WithFaker::class);

beforeEach(function () {
    $this->seed();

    $this->adminUser = User::where('email', 'admin@mail.com')->first();
    $this->nonAdminUser = User::where('email', 'user@mail.com')->first();
});

test('Admin User can list all users', function () {
    $this->actingAs($this->adminUser, 'api')
         ->get('/api/users')
         ->assertOk()
         ->assertJsonCount(2, 'data')
         ->assertSeeText('admin@mail.com')
         ->assertSeeText('user@mail.com');
});

test('Non admin User cannot list all users', function () {
    $this->actingAs($this->nonAdminUser, 'api')
         ->get('/api/users')
         ->assertForbidden();
});

test('Non logged User cannot list all users', function () {
    $this->get('/api/users')
         ->assertRedirect('/');

    $this->withHeaders(['Accept' => 'application/json'])
         ->get('/api/users')
         ->assertUnauthorized();
});

test('Admin User can show a user', function () {
    $this->actingAs($this->adminUser, 'api')
         ->get('/api/users/' . $this->adminUser->id)
         ->assertOk()
         ->assertJsonFragment(['email' => 'admin@mail.com'])
         ->assertJsonFragment(['is_admin' => true]);
});

test('Non admin User can only show his own user', function () {
    $this->actingAs($this->nonAdminUser, 'api')
         ->get('/api/users/' . $this->adminUser->id)
         ->assertForbidden();

    $this->actingAs($this->nonAdminUser, 'api')
         ->get('/api/users/' . $this->nonAdminUser->id)
         ->assertOk();
});

test('Admin User can create a user', function () {
    $data = ['name'     => $this->faker->name,
             'email'    => $this->faker->email,
             'is_admin' => true,
             'password' => '123456'];

    $result = $this->actingAs($this->adminUser, 'api')
                   ->post('/api/users', $data)
                   ->assertCreated()
                   ->assertJsonFragment(['success' => true])
                   ->assertJsonFragment(['message' => 'New user successfully created'])
                   ->assertJsonFragment(['name' => $data['name']])
                   ->assertJsonFragment(['email' => $data['email']])
                   ->assertJsonFragment(['is_admin' => true]);

    // check the password has been hashed in db
    $user = User::find($result['data']['id']);
    $this->assertNotEquals('123456', $user->password);
});

test('Non admin User cannot create a user', function () {
    $data = ['name'     => $this->faker->name,
             'email'    => $this->faker->email,
             'is_admin' => true,
             'password' => '123456'];

    $this->actingAs($this->nonAdminUser, 'api')
         ->post('/api/users', $data)
         ->assertForbidden();
});

test('Admin User can update a user', function () {
    $user = User::factory()->create();

    $newData = ['name' => 'new name', 'email' => 'newemail@mail.com', 'password' => 'new'];

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/users/' . $user->id, $newData)
         ->assertOk()
         ->assertJsonFragment(['message' => 'User successfully updated'])
         ->assertJsonFragment(['name' => $newData['name']])
         ->assertJsonFragment(['email' => $newData['email']]);
});

test('Non admin User cannot update a user', function () {
    $user = User::factory()->create();

    $this->actingAs($this->nonAdminUser, 'api')
         ->put('/api/users/' . $user->id, ['name' => 'new name'])
         ->assertForbidden();
});

test('Admin User can delete a user', function () {
    $previousCount = User::count();
    $user = User::factory()->create();
    $this->assertCount($previousCount + 1, User::all());

    $this->actingAs($this->adminUser, 'api')
         ->delete('/api/users/' . $user->id)
         ->assertNoContent();

    $this->assertNull(User::find($user->id));
    $this->assertCount($previousCount, User::all());
});

test('Non admin User cannot delete a user', function () {
    $user = User::factory()->create();

    $this->actingAs($this->nonAdminUser, 'api')
         ->delete('/api/users/' . $user->id)
         ->assertForbidden();
});

test('Admin User can change admin status', function () {
    $user = User::factory()->create();

    $this->assertFalse($user->is_admin);

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/users/' . $user->id, ['is_admin' => true])
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['message' => 'User successfully updated']);
    $this->assertTrue($user->fresh()->is_admin);

    // no is_admin field
    $this->actingAs($this->adminUser, 'api')
         ->put('/api/users/' . $user->id, [])
         ->assertOk()
         ->assertJsonFragment(['success' => true]);

    $this->assertTrue($user->fresh()->is_admin);

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/users/' . $user->id, ['is_admin' => false])
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['message' => 'User successfully updated']);

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/users/' . $user->id, ['is_admin' => 'fake'])
         ->assertStatus(422)
         ->assertJsonFragment(['success' => false])
         ->assertJsonFragment(['is_admin' => ['The is admin field must be true or false.']]);

    $this->assertFalse($user->fresh()->is_admin);
});

test('Non admin User cannot change admin status', function () {
    $user = User::factory()->create();

    $this->actingAs($this->nonAdminUser, 'api')
         ->put('/api/users/' . $user->id, ['is_admin' => true])
         ->assertForbidden();
});

