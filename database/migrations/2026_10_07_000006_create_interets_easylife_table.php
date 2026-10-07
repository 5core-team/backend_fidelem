<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interets_easylife', function (Blueprint $table) {
            $table->id();
            $table->string('prenom', 100);
            $table->string('nom', 100);
            $table->string('telephone', 30);
            $table->string('email')->nullable();
            $table->string('profil', 100)->nullable();
            $table->string('pole', 100)->nullable();
            $table->text('message')->nullable();
            $table->json('rendez_vous')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interets_easylife');
    }
};
