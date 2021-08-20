<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        $image = $this->faker->image(storage_path() . '/app/public');
        $file_info = pathinfo($image);

        return [
            'name'            => $this->faker->name,
            'surname'         => $this->faker->lastName,
            'photo_file'      => 'public/' . $file_info['basename'],
            'creator_user_id' => User::all()->random()->id,
            //'updater_user_id' => User::factory(), -> in case we want to create a new one
            'updater_user_id' => User::all()->random()->id,
        ];
    }

    public function noUsers()
    {
        return $this->state(function (array $attributes) {
            return [
                'creator_user_id' => null,
                'updater_user_id' => null,
            ];
        });
    }
}
