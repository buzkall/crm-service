<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (!DB::table('oauth_clients')->count()) { // if no oauth tokens
            Artisan::call('passport:install');
        }

        if (!User::count()) { // if no users
            $password = 'crm,service';
            User::factory()->admin()->create(['email' => 'admin@mail.com', 'password' => bcrypt($password)]);
            User::factory()->notAdmin()->create(['email' => 'user@mail.com', 'password' => bcrypt($password)]);

            $this->command->info(' -> 🤖 Created Admin and User users');
        }

        $this->call(CustomerSeeder::class);
    }
}
