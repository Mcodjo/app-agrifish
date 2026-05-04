<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password123!');

        User::updateOrCreate(
            ['email' => 'client@agrifish.africa'],
            [
                'name' => 'Client Démo',
                'password' => $password,
                'role' => 'client',
                'phone' => '+2250102030405',
                'preferences' => [
                    'service_updates' => true,
                    'status_notifications' => true,
                    'newsletter' => false,
                    'preferred_contact' => 'email',
                ],
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@agrifish.africa'],
            [
                'name' => 'Admin AgriFish',
                'password' => $password,
                'role' => 'admin',
                'phone' => '+2250102030406',
                'preferences' => [
                    'service_updates' => true,
                    'status_notifications' => true,
                    'newsletter' => false,
                    'preferred_contact' => 'email',
                ],
            ]
        );

        User::updateOrCreate(
            ['email' => 'expert@agrifish.africa'],
            [
                'name' => 'Expert AgriFish',
                'password' => $password,
                'role' => 'expert',
                'phone' => '+2250102030407',
                'preferences' => [
                    'service_updates' => true,
                    'status_notifications' => true,
                    'newsletter' => false,
                    'preferred_contact' => 'email',
                ],
            ]
        );
    }
}
