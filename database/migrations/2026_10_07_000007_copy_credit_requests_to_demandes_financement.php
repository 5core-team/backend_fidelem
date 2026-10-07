<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend les demandes de l'ancienne table credit_requests dans demandes_financement.
 *
 * L'ancienne table est conservée : elle pourra être supprimée une fois la reprise
 * vérifiée en production. La migration peut être relancée sans créer de doublon.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('credit_requests')) {
            return;
        }

        DB::table('credit_requests')
            ->leftJoin('users as usager', 'usager.id', '=', 'credit_requests.user_id')
            ->leftJoin('users as conseiller', 'conseiller.id', '=', 'usager.created_by')
            ->select(
                'credit_requests.*',
                'usager.name as usager_prenom',
                'usager.last_name as usager_nom',
                'usager.phone as usager_telephone',
                'usager.email as usager_email',
                'conseiller.id as conseiller_id',
                'conseiller.type_compte as conseiller_type',
                'conseiller.zone as conseiller_zone',
            )
            ->orderBy('credit_requests.id')
            ->chunk(200, function ($lignes) {
                foreach ($lignes as $ligne) {
                    if (DB::table('demandes_financement')->where('credit_request_id', $ligne->id)->exists()) {
                        continue;
                    }

                    [$financement, $objet] = $this->decouperObjet((string) $ligne->purpose);
                    $estConseiller = $ligne->conseiller_type === 'advisor';

                    DB::table('demandes_financement')->insert([
                        'user_id' => $ligne->user_id,
                        'conseiller_id' => $estConseiller ? $ligne->conseiller_id : null,
                        'prenom' => $ligne->usager_prenom ?? 'Usager',
                        'nom' => $ligne->usager_nom ?? '',
                        'telephone' => $ligne->usager_telephone ?? '',
                        'email' => $ligne->usager_email,
                        'financement' => $financement,
                        'objet' => $objet,
                        'montant' => (int) round((float) $ligne->amount),
                        'duree' => (int) $ligne->duration,
                        'message' => $ligne->additional_details,
                        'zone' => $estConseiller ? $ligne->conseiller_zone : null,
                        'rendez_vous' => null,
                        'statut' => $this->convertirStatut((string) $ligne->status),
                        'origine' => 'historique',
                        'credit_request_id' => $ligne->id,
                        'created_at' => $ligne->created_at,
                        'updated_at' => $ligne->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('demandes_financement')->whereNotNull('credit_request_id')->delete();
    }

    /** « Immobilier · Achat de terrain » donne ['immobilier', 'Achat de terrain']. */
    private function decouperObjet(string $purpose): array
    {
        $libelles = ['immobilier' => 'immobilier', 'transport' => 'transport', 'affaires' => 'affaires'];
        $parties = array_map('trim', explode('·', $purpose, 2));
        $prefixe = mb_strtolower($parties[0]);

        if (count($parties) === 2 && isset($libelles[$prefixe])) {
            return [$libelles[$prefixe], $parties[1]];
        }

        $texte = mb_strtolower($purpose);
        foreach (['immobilier' => ['immobil', 'maison', 'terrain', 'logement', 'construction'],
            'transport' => ['transport', 'véhicule', 'vehicule', 'voiture', 'moto', 'taxi', 'camion']] as $slug => $mots) {
            foreach ($mots as $mot) {
                if (str_contains($texte, $mot)) {
                    return [$slug, $purpose];
                }
            }
        }

        return ['affaires', $purpose];
    }

    private function convertirStatut(string $ancien): string
    {
        $valeur = mb_strtolower($ancien);

        return match (true) {
            str_starts_with($valeur, 'approuv') => 'Acceptée',
            str_starts_with($valeur, 'rejet') => 'Refusée',
            default => 'Dossier en cours',
        };
    }
};
