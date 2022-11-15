<?php

namespace Database\Seeders;

use App\Models\User;
// use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'role' => '0',
            'name' => 'user', 
            'email' => 'user@example.com',
            'password' => Hash::make('user'),
            'createdBy' =>'user',
            'deletedBy'=>'Null'
        ]);
    }
}