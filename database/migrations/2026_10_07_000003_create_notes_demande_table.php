<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes_demande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_financement_id')->constrained('demandes_financement')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('texte');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes_demande');
    }
};
