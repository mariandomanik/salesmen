<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Generate a random user, testing purposes only
     * @return mixed
     */
    public static function generateUser(): mixed {
        $faker = \Faker\Factory::create();
        return User::create([
            'name' => 'randomName',
            'email' => $faker->email,
            'password' => Hash::make('password'),
        ]);
    }
}
