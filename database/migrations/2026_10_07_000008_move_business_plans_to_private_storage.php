<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * L'ancienne fonction « levée de fonds » rangeait les business plans dans le stockage
 * public (storage/app/public/uploads), servi tel quel par /storage. Ces documents
 * d'entreprise sont déplacés dans un dossier privé, hors de portée du web.
 */
return new class extends Migration
{
    private const SOURCE = 'public/uploads';

    private const CIBLE = 'prive/business-plans';

    public function up(): void
    {
        $disque = Storage::disk('local');

        if (Schema::hasTable('funding_requests')) {
            foreach (DB::table('funding_requests')->whereNotNull('businessPlan')->get(['id', 'businessPlan']) as $ligne) {
                $nom = basename((string) $ligne->businessPlan);
                if ($disque->exists(self::SOURCE.'/'.$nom)) {
                    $disque->move(self::SOURCE.'/'.$nom, self::CIBLE.'/'.$nom);
                }
                DB::table('funding_requests')->where('id', $ligne->id)->update(['businessPlan' => self::CIBLE.'/'.$nom]);
            }
        }

        foreach ($disque->files(self::SOURCE) as $fichier) {
            $disque->move($fichier, self::CIBLE.'/'.basename($fichier));
        }
    }

    public function down(): void
    {
        // Les documents restent privés : on ne les republie pas.
    }
};
