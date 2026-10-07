<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * En local et en test uniquement : comptes et demandes d'exemple.
     * Ailleurs, le premier responsable se crée avec `php artisan fidelem:responsable`.
     */
    public function run(): void
    {
        // Les comptes d'exemple ont un mot de passe connu : seulement en local et en test.
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn("Aucune donnée d'exemple hors local. Utilisez php artisan fidelem:responsable.");

            return;
        }

        $this->call(DemoSeeder::class);
    }
}
