<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
uses(WithFaker::class);

beforeEach(function () {
    $this->seed();

    $this->adminUser = User::where('email', 'admin@mail.com')->first();
    $this->nonAdminUser = User::where('email', 'user@mail.com')->first();
});

test('Admin user can list all customers', function () {
    $before = Customer::count(); // from seeder
    $count = rand(2, 5);
    $this->actingAs($this->adminUser); // for the creator_user_id
    $customers = Customer::factory()->noUsers()->times($count)->create();

    $this->actingAs($this->adminUser, 'api')
         ->get('/api/customers')
         ->assertOk()
         ->assertJsonCount($before + $count, 'data')
         ->assertSeeText(e($customers[0]->name))
         ->assertJsonFragment(['surname' => e($customers[0]->surname)])
         ->assertJsonFragment(['creator_user_id' => $this->adminUser->id]);

    // delete photos from storage
    $customers->each->delete();
})->group('now');

test('Non Admin user can list all customers', function () {
    $before = Customer::count(); // from seeder
    $count = rand(2, 5);
    $this->actingAs($this->adminUser); // for the creator_user_id
    $customers = Customer::factory()->noUsers()->times($count)->create();
    $this->actingAs($this->nonAdminUser, 'api')
         ->get('/api/customers')
         ->assertOk()
         ->assertJsonCount($before + $count, 'data')
         ->assertSeeText(e($customers[0]->name))
         ->assertJsonFragment(['surname' => e($customers[0]->surname)])
         ->assertJsonFragment(['creator_user_id' => $this->adminUser->id]);

    // delete photos from storage
    $customers->each->delete();
});

test('Admin user can show a customer', function () {
    $this->actingAs($this->nonAdminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    $this->actingAs($this->adminUser, 'api')
         ->get('/api/customers/' . $customer->id)
         ->assertOk()
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['name' => e($customer['name'])])
         ->assertJsonFragment(['surname' => e($customer['surname'])])
         ->assertJsonFragment(['creator_user_id' => $this->nonAdminUser->id])
         ->assertJsonFragment(['updater_user_id' => null]);

    $dbCustomer = Customer::find($customer->id);
    Storage::disk('local')->assertExists($dbCustomer->photo_file);

    // delete photos from storage
    $customer->delete();
});

test('Non admin user can show a customer', function () {
    $this->actingAs($this->nonAdminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    $this->actingAs($this->nonAdminUser, 'api')
         ->get('/api/customers/' . $customer->id)
         ->assertOk()
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['name' => e($customer['name'])])
         ->assertJsonFragment(['surname' => e($customer['surname'])])
         ->assertJsonFragment(['creator_user_id' => $this->nonAdminUser->id])
         ->assertJsonFragment(['updater_user_id' => null]);

    $dbCustomer = Customer::find($customer->id);
    Storage::disk('local')->assertExists($dbCustomer->photo_file);

    // delete photos from storage
    $customer->delete();
});

test('Admin & Non Admin Users try to create a customer without data', function () {
    $this->actingAs($this->adminUser, 'api')
         ->post('/api/customers', [])
         ->assertStatus(422)
         ->assertJsonFragment(['name' => ['The name field is required.']])
         ->assertJsonFragment(['surname' => ['The surname field is required.']]);
});

test('Admin user can create a customer and upload a photo', function () {
    Storage::fake('photos'); // storage/framework/testing/disks/photos/
    $file = UploadedFile::fake()->image('photo.jpg');

    $data = ['name'       => $this->faker->name,
             'surname'    => $this->faker->lastName,
             'photo_file' => $file];

    $this->actingAs($this->adminUser, 'api')
         ->post('/api/customers', $data)
         ->assertCreated()
         ->assertJsonFragment(['photo_file' => 'public/' . $file->hashName()])
         ->assertJsonFragment(['photo_url' => asset('storage/public/' . $file->hashName())])
         ->assertJsonFragment(['name' => e($data['name'])])
         ->assertJsonFragment(['surname' => e($data['surname'])])
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['creator_user_id' => $this->adminUser->id])
         ->assertJsonFragment(['updater_user_id' => $this->adminUser->id]);
});

test('Non Admin user can create a customer and upload a photo', function () {
    Storage::fake('photos'); // storage/framework/testing/disks/photos/
    $file = UploadedFile::fake()->image('photo.jpg');

    $data = ['name'       => $this->faker->name,
             'surname'    => $this->faker->lastName,
             'photo_file' => $file];

    $this->actingAs($this->nonAdminUser, 'api')
         ->post('/api/customers', $data)
         ->assertCreated()
         ->assertJsonFragment(['photo_file' => 'public/' . $file->hashName()])
         ->assertJsonFragment(['photo_url' => asset('storage/public/' . $file->hashName())])
         ->assertJsonFragment(['name' => e($data['name'])])
         ->assertJsonFragment(['surname' => e($data['surname'])])
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['creator_user_id' => $this->nonAdminUser->id])
         ->assertJsonFragment(['updater_user_id' => $this->nonAdminUser->id]);
});

test('Admin user can update a customer', function () {
    $this->actingAs($this->adminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    $data = ['name'    => 'new name',
             'surname' => 'new surname'];

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/customers/' . $customer->id, $data)
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['name' => e($data['name'])])
         ->assertJsonFragment(['surname' => e($data['surname'])])
         ->assertJsonFragment(['photo_file' => $customer['photo_file']]) // hasn't change
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['creator_user_id' => $this->adminUser->id])
         ->assertJsonFragment(['updater_user_id' => $this->adminUser->id]);

    // now upload a new file
    $file = UploadedFile::fake()->image('photo.jpg');
    $data = ['photo_file' => $file];

    $this->actingAs($this->adminUser, 'api')
         ->put('/api/customers/' . $customer->id, $data)
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['photo_file' => 'public/' . $file->hashName()])
         ->assertJsonFragment(['photo_url' => asset('storage/public/' . $file->hashName())])
         ->assertJsonFragment(['updater_user_id' => $this->adminUser->id]);

    // delete photos from storage
    // force the fresh to get the new photo url
    $customer->fresh()->delete();
})->group('now');

test('Non Admin user can update a customer', function () {
    $this->actingAs($this->adminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    $data = ['name'    => 'new name',
             'surname' => 'new surname'];

    $this->actingAs($this->nonAdminUser, 'api')
         ->put('/api/customers/' . $customer->id, $data)
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['name' => $data['name']])
         ->assertJsonFragment(['surname' => $data['surname']])
         ->assertJsonFragment(['photo_file' => $customer['photo_file']]) // hasn't change
         ->assertJsonStructure(['data' => ['id', 'name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id', 'photo_url']])
         ->assertJsonFragment(['creator_user_id' => $this->adminUser->id])
         ->assertJsonFragment(['updater_user_id' => $this->nonAdminUser->id]);

    // now upload a new file
    $file = UploadedFile::fake()->image('photo.jpg');
    $data = ['photo_file' => $file];

    $this->actingAs($this->nonAdminUser, 'api')
         ->put('/api/customers/' . $customer->id, $data)
         ->assertOk()
         ->assertJsonFragment(['success' => true])
         ->assertJsonFragment(['photo_file' => 'public/' . $file->hashName()])
         ->assertJsonFragment(['photo_url' => asset('storage/public/' . $file->hashName())])
         ->assertJsonFragment(['updater_user_id' => $this->nonAdminUser->id]);

    // delete photos from storage
    // force the fresh to get the new photo url
    $customer->delete();
});

test('Admin user can delete a customer', function () {
    $this->actingAs($this->adminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    Storage::disk('local')->assertExists($customer->photo_file);

    $this->actingAs($this->adminUser, 'api')
         ->delete('/api/customers/' . $customer->id)
         ->assertNoContent();

    expect(Customer::find($customer->id))->toBeNull();

    Storage::disk('local')->assertMissing($customer->photo_file);
});

test('Non Admin user can delete a customer', function () {
    $this->actingAs($this->nonAdminUser); // for the creator_user_id
    $customer = Customer::factory()->noUsers()->create();

    Storage::disk('local')->assertExists($customer->photo_file);

    $this->actingAs($this->nonAdminUser, 'api')
         ->delete('/api/customers/' . $customer->id)
         ->assertNoContent();

    expect(Customer::find($customer->id))->toBeNull();

    Storage::disk('local')->assertMissing($customer->photo_file);
});