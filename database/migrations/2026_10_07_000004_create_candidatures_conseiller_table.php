<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures_conseiller', function (Blueprint $table) {
            $table->id();
            // Compte conseiller créé « en attente » avec la candidature.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('prenom', 100);
            $table->string('nom', 100);
            $table->string('telephone', 30);
            $table->string('email');
            $table->string('niveau_vise', 50);
            $table->string('situation', 50);
            $table->text('experience')->nullable();
            $table->json('rendez_vous')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures_conseiller');
    }
};
