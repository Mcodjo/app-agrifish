<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Données de démo (mots de passe connus) : jamais en production.
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Données de démo ignorées : environnement « '.app()->environment().' ».');

            return;
        }

        $this->call([
            DemoUsersSeeder::class,
            DemoProjectSeeder::class,
            DemoShopSeeder::class,
        ]);
    }
}
