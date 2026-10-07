<?php

namespace App\Http\Controllers;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/** Back-office : chiffres de la vue d'ensemble. */
class StatistiqueController extends Controller
{
    public function comptes(): JsonResponse
    {
        return response()->json([
            'totalUsers' => User::where('type_compte', User::USAGER)->count(),
            'totalAdvisors' => User::where('type_compte', User::CONSEILLER)->count(),
            'totalManagers' => User::where('type_compte', User::RESPONSABLE)->count(),
            'pendingUsers' => User::where('statut', User::EN_ATTENTE)->count(),
        ]);
    }

    public function demandes(): JsonResponse
    {
        $depuis = now()->startOfMonth()->subMonths(11);
        $recentes = DemandeFinancement::where('created_at', '>=', $depuis)->get(['montant', 'created_at']);

        $parMois = collect(range(0, 11))->map(function (int $i) use ($depuis, $recentes) {
            $mois = $depuis->copy()->addMonths($i);
            $lignes = $recentes->filter(fn ($d) => $d->created_at->format('Y-m') === $mois->format('Y-m'));

            return [
                'mois' => $mois->format('Y-m'),
                'nombre' => $lignes->count(),
                'montant' => (int) $lignes->sum('montant'),
            ];
        });

        $parStatut = collect(config('fidelem.statuts'))
            ->mapWithKeys(fn (string $s) => [$s => DemandeFinancement::where('statut', $s)->count()]);

        return response()->json([
            'total' => DemandeFinancement::count(),
            'montantTotal' => (int) DemandeFinancement::sum('montant'),
            'parStatut' => $parStatut,
            'parMois' => $parMois,
        ]);
    }
}
