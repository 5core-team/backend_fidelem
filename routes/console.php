<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

/*
| Crée (ou réactive) un compte responsable. L'inscription publique étant fermée,
| c'est ainsi que l'on crée le premier compte du back-office.
*/
Artisan::command('fidelem:responsable {email} {--prenom=Responsable} {--nom=FIDELEM}', function (string $email) {
    $motDePasse = $this->secret('Mot de passe (8 caractères minimum)');

    $validation = Validator::make(['email' => $email, 'password' => $motDePasse], [
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    if ($validation->fails()) {
        foreach ($validation->errors()->all() as $erreur) {
            $this->error($erreur);
        }

        return 1;
    }

    $user = User::updateOrCreate(['email' => mb_strtolower($email)], [
        'name' => $this->option('prenom'),
        'last_name' => $this->option('nom'),
        'type_compte' => User::RESPONSABLE,
        'statut' => User::ACTIF,
        'password' => $motDePasse,
    ]);

    $this->info("Compte responsable prêt : {$user->email}");

    return 0;
})->purpose('Créer ou réactiver un compte responsable FIDELEM');
