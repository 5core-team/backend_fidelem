<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Zone de gestion, niveau et spécialités d'un conseiller financier.
            $table->string('zone')->nullable()->after('address')->index();
            $table->string('niveau', 20)->nullable()->after('zone');
            $table->json('financements')->nullable()->after('niveau');
            $table->string('photo')->nullable()->after('financements');
        });

        // Supprimer un conseiller ne doit pas être bloqué par les clients qu'il a créés.
        // SQLite ne sait pas modifier une clé étrangère existante : la règle n'est
        // appliquée qu'en MySQL, le contrôleur détache aussi les clients avant suppression.
        if (DB::getDriverName() === 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->foreign('created_by')->references('id')->on('users');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['zone']);
            $table->dropColumn(['zone', 'niveau', 'financements', 'photo']);
        });
    }
};
