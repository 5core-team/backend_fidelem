<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeEspaceRequest;
use App\Http\Requests\DemandePubliqueRequest;
use App\Http\Resources\DemandeResource;
use App\Models\DemandeFinancement;
use App\Models\User;
use App\Notifications\NouvelleDemandeNotification;
use App\Notifications\RendezVousConfirmeNotification;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class DemandeFinancementController extends Controller
{
    /** Relations chargées pour chaque demande renvoyée au front. */
    private const AVEC = ['conseiller', 'notes.auteur'];

    /* ------------------------------------------------------------------ */
    /* Création */
    /* ------------------------------------------------------------------ */

    /** Site public : demande de financement ou prise de rendez-vous. */
    public function storePublic(DemandePubliqueRequest $request): JsonResponse
    {
        $donnees = $request->validated();
        $rdv = $request->rendezVous();

        $demande = DemandeFinancement::create([
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'telephone' => $donnees['telephone'],
            'email' => isset($donnees['email']) ? mb_strtolower($donnees['email']) : null,
            'financement' => $donnees['financement'],
            'objet' => $donnees['objet'],
            'montant' => $donnees['montant'],
            'duree' => $donnees['duree'],
            'message' => $donnees['message'] ?? null,
            'zone' => $rdv['zone'] ?? null,
            'rendez_vous' => $rdv,
            'conseiller_id' => $donnees['conseillerId'] ?? null,
            'statut' => DemandeFinancement::NOUVELLE,
            'origine' => 'site',
        ]);

        $this->prevenirConseillers($demande);

        return (new DemandeResource($demande->load(self::AVEC)))->response()->setStatusCode(201);
    }

    /** Espaces connectés : l'usager pour lui-même, le conseiller pour l'un de ses clients. */
    public function storeEspace(DemandeEspaceRequest $request): JsonResponse
    {
        $auteur = $request->user();
        $donnees = $request->validated();
        $rdv = $request->rendezVous();
        [$financement, $objet] = $this->decouperObjet($donnees['purpose'], $donnees['financement'] ?? null);

        if ($auteur->estConseiller()) {
            $usager = $auteur->clients()->find($donnees['clientId']);
            abort_if(! $usager, 403, "Ce client n'est pas rattaché à votre compte.");
            $conseiller = $auteur;
        } else {
            $usager = $auteur;
            $conseiller = $usager->advisor && $usager->advisor->estActif() && $usager->advisor->estConseiller() ? $usager->advisor : null;
        }

        $demande = DemandeFinancement::create([
            'user_id' => $usager->id,
            'conseiller_id' => $conseiller?->id,
            'prenom' => $usager->name,
            'nom' => $usager->last_name,
            'telephone' => $usager->phone ?? '',
            'email' => $usager->email,
            'financement' => $financement,
            'objet' => $objet,
            'montant' => $donnees['amount'],
            'duree' => $donnees['duration'],
            'message' => $donnees['additional_details'] ?? null,
            'zone' => $rdv['zone'] ?? $donnees['zone'] ?? $conseiller?->zone,
            'rendez_vous' => $rdv,
            // Créée par le conseiller, la demande est déjà suivie ; créée par l'usager, elle attend sa prise en charge.
            'statut' => $auteur->estConseiller() ? DemandeFinancement::PRISE_EN_CHARGE : DemandeFinancement::NOUVELLE,
            'pris_en_charge_le' => $auteur->estConseiller() ? now() : null,
            'origine' => 'espace',
        ]);

        if (! $auteur->estConseiller()) {
            $this->prevenirConseillers($demande);
        }

        return (new DemandeResource($demande->load(self::AVEC)))->response()->setStatusCode(201);
    }

    /* ------------------------------------------------------------------ */
    /* Listes */
    /* ------------------------------------------------------------------ */

    /** Mon espace : les demandes de l'usager connecté. */
    public function indexUsager(Request $request): AnonymousResourceCollection
    {
        return DemandeResource::collection(
            $request->user()->demandes()->with(self::AVEC)->latest()->get()
        );
    }

    /** Espace Conseiller : demandes de la zone et demandes adressées au conseiller, pas encore prises en charge. */
    public function indexZone(Request $request): AnonymousResourceCollection
    {
        return DemandeResource::collection(
            DemandeFinancement::aPrendreEnCharge($request->user())->with(self::AVEC)->latest()->get()
        );
    }

    /** Espace Conseiller : dossiers suivis. Le paramètre userId envoyé par le front est ignoré. */
    public function indexConseiller(Request $request): AnonymousResourceCollection
    {
        return DemandeResource::collection(
            DemandeFinancement::suiviesPar($request->user())->with(self::AVEC)->latest()->get()
        );
    }

    /** Back-office : toutes les demandes. */
    public function indexResponsable(): AnonymousResourceCollection
    {
        return DemandeResource::collection(DemandeFinancement::with(self::AVEC)->latest()->get());
    }

    /* ------------------------------------------------------------------ */
    /* Suivi d'un dossier */
    /* ------------------------------------------------------------------ */

    public function prendreEnCharge(Request $request, DemandeFinancement $demande): DemandeResource|JsonResponse
    {
        $conseiller = $request->user();

        if ($demande->statut !== DemandeFinancement::NOUVELLE) {
            // Un second clic du même conseiller ne doit pas afficher d'erreur.
            return $demande->conseiller_id === $conseiller->id
                ? new DemandeResource($demande->load(self::AVEC))
                : $this->dejaPrise();
        }

        $this->authorize('prendreEnCharge', $demande);

        // Mise à jour conditionnelle : si deux conseillers cliquent en même temps, un seul l'emporte.
        $pris = DemandeFinancement::whereKey($demande->id)
            ->where('statut', DemandeFinancement::NOUVELLE)
            ->where(fn ($q) => $q->whereNull('conseiller_id')->orWhere('conseiller_id', $conseiller->id))
            ->update([
                'conseiller_id' => $conseiller->id,
                'statut' => DemandeFinancement::PRISE_EN_CHARGE,
                'pris_en_charge_le' => now(),
                'updated_at' => now(),
            ]);

        if (! $pris) {
            return $this->dejaPrise();
        }

        return new DemandeResource($demande->refresh()->load(self::AVEC));
    }

    public function changerStatut(Request $request, DemandeFinancement $demande): DemandeResource
    {
        $this->authorize('modifier', $demande);

        $donnees = $request->validate([
            'statut' => ['required', 'string', Rule::in(config('fidelem.statuts'))],
        ]);

        $demande->update(['statut' => $donnees['statut']]);

        return new DemandeResource($demande->load(self::AVEC));
    }

    public function fixerRendezVous(Request $request, DemandeFinancement $demande): DemandeResource
    {
        $this->authorize('modifier', $demande);

        $donnees = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'creneau' => ['required', 'string', Rule::in(config('fidelem.rendez_vous.creneaux'))],
            'mode' => ['nullable', 'string', Rule::in(config('fidelem.rendez_vous.modes'))],
        ], [], ['date' => 'date du rendez-vous', 'creneau' => 'créneau']);

        $demande->update([
            'rendez_vous' => array_merge($demande->rendez_vous ?? [], array_filter($donnees)),
            'statut' => DemandeFinancement::RENDEZ_VOUS_FIXE,
        ]);

        Notifier::envoyerA($demande->email, new RendezVousConfirmeNotification($demande->load('conseiller')));

        return new DemandeResource($demande->load(self::AVEC));
    }

    /* ------------------------------------------------------------------ */

    private function dejaPrise(): JsonResponse
    {
        return response()->json(['message' => 'Cette demande a déjà été prise en charge par un autre conseiller.'], 409);
    }

    /** Prévient le conseiller choisi, ou à défaut les conseillers actifs de la zone. */
    private function prevenirConseillers(DemandeFinancement $demande): void
    {
        $destinataires = $demande->conseiller_id
            ? User::whereKey($demande->conseiller_id)->get()
            : ($demande->zone ? User::conseillersActifs()->where('zone', $demande->zone)->get() : collect());

        if ($destinataires->isNotEmpty()) {
            Notifier::envoyer($destinataires, new NouvelleDemandeNotification($demande));
        }
    }

    /** « Immobilier · Achat de terrain » donne ['immobilier', 'Achat de terrain']. */
    private function decouperObjet(string $purpose, ?string $financement): array
    {
        $parties = array_map('trim', explode('·', $purpose, 2));
        $slug = array_search(mb_strtolower($parties[0]), array_map('mb_strtolower', config('fidelem.financements')), true);

        if (count($parties) === 2 && $slug !== false) {
            return [$financement ?? $slug, $parties[1]];
        }

        return [$financement ?? 'affaires', $purpose];
    }
}
