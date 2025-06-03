<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
           Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name'); // Added last name field
            $table->string('email')->unique();
            $table->string('phone')->nullable(); // Added phone field
            $table->string('address')->nullable(); // Added address field
            $table->enum('type_compte', ['user', 'advisor', 'manager'])->default('user');
            $table->enum('statut', ['En attente', 'Actif', 'Rejeté'])->default('En attente');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
