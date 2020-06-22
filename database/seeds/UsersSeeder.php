<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        collect([
            [
                'name' => 'Baris',
                'username' => 'baris',
                'email' => 'baris@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Jane Doe',
                'username' => 'janedoe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
            ],
        ])->each(function ($user) {
            factory(User::class)->create(
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'password' => bcrypt('password')
                ]
            );
        });
    }
}
