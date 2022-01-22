<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('users')->insert([
            [
                'firstname' => 'Sami',
                'lastname' => 'de molengeek',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('oklm'), // password
                'picture' => 'Molengeek_Logo.jpg',
                'remember_token' => Str::random(10),                
            ]
        ]);
    }
}
