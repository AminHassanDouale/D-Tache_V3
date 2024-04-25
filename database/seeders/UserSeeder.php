<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'amin',
            'number' => '77049495',
            'email' => 'amin.hassan@d-money.dj',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'department_id' => '1',
            'avatar' => 'https://picsum.photos/200?x=' . rand(),
        ]);
    }
}
