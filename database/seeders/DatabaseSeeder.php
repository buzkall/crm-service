<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (!User::count()) { // if empty
            Artisan::call('passport:install');

            $password = 'crm,service';
            User::factory()->admin()->create(['email' => 'admin@mail.com', 'password' => bcrypt($password)]);
            User::factory()->notAdmin()->create(['email' => 'user@mail.com', 'password' => bcrypt($password)]);

            $this->command->info(' -> 🤖 Created Admin and User users');
        }
    }
}
