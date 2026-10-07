<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_financement', function (Blueprint $table) {
            $table->id();
            // Compte usager rattaché (absent tant que la demande vient d'un visiteur du site).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('conseiller_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('prenom', 100);
            $table->string('nom', 100);
            $table->string('telephone', 30);
            $table->string('email')->nullable();

            $table->string('financement', 20);
            $table->string('objet');
            $table->unsignedBigInteger('montant')->default(0);
            $table->unsignedSmallInteger('duree')->default(0);
            $table->text('message')->nullable();

            $table->string('zone')->nullable()->index();
            $table->json('rendez_vous')->nullable();
            $table->string('statut', 30)->default('Nouvelle')->index();

            // site : formulaire public ; espace : créée depuis un espace connecté ;
            // historique : reprise de l'ancienne table credit_requests.
            $table->string('origine', 20)->default('site');
            $table->unsignedBigInteger('credit_request_id')->nullable()->unique();
            $table->timestamp('pris_en_charge_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_financement');
    }
};
