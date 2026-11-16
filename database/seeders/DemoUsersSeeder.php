<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes de démonstration — uniquement pour les environnements local / testing
 * (voir DatabaseSeeder). Mot de passe configurable via DEMO_USER_PASSWORD.
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(env('DEMO_USER_PASSWORD', 'Password123!'));

        $preferences = [
            'service_updates' => true,
            'status_notifications' => true,
            'newsletter' => false,
            'preferred_contact' => 'email',
        ];

        foreach ([
            ['email' => 'client@agrifish.africa', 'name' => 'Client Démo', 'role' => 'client', 'phone' => '+2250102030405'],
            ['email' => 'admin@agrifish.africa', 'name' => 'Admin AgriFish', 'role' => 'admin', 'phone' => '+2250102030406'],
            ['email' => 'expert@agrifish.africa', 'name' => 'Expert AgriFish', 'role' => 'expert', 'phone' => '+2250102030407'],
        ] as $account) {
            $user = User::firstOrNew(['email' => $account['email']]);

            $user->forceFill([
                'name' => $account['name'],
                'password' => $password,
                'role' => $account['role'],
                'phone' => $account['phone'],
                'preferences' => $preferences,
                'email_verified_at' => now(),
            ])->save();
        }
    }
}
