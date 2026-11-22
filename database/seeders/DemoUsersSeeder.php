<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes de démonstration, pour les environnements local et testing.
 * Mot de passe : variable DEMO_USER_PASSWORD.
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // env() renvoie une chaîne vide quand la variable existe sans valeur.
        $password = Hash::make(env('DEMO_USER_PASSWORD') ?: 'Password123!');

        $preferences = [
            'message_notifications' => true,
            'status_notifications' => true,
            'newsletter' => false,
            'preferred_contact' => 'email',
        ];

        foreach ([
            ['email' => 'client@agrifish.africa', 'name' => 'Mariam Ouattara', 'role' => 'client', 'phone' => '+2250102030405'],
            ['email' => 'admin@agrifish.africa', 'name' => 'Administration AgriFish', 'role' => 'admin', 'phone' => '+2250102030406'],
            ['email' => 'expert@agrifish.africa', 'name' => 'Ibrahima Sow', 'role' => 'expert', 'phone' => '+2250102030407'],
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
