<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandeFinancement extends Model
{
    use HasFactory;

    public const NOUVELLE = 'Nouvelle';

    public const PRISE_EN_CHARGE = 'Prise en charge';

    public const RENDEZ_VOUS_FIXE = 'Rendez-vous fixé';

    protected $table = 'demandes_financement';

    protected $fillable = [
        'user_id',
        'conseiller_id',
        'prenom',
        'nom',
        'telephone',
        'email',
        'financement',
        'objet',
        'montant',
        'duree',
        'message',
        'zone',
        'rendez_vous',
        'statut',
        'origine',
        'credit_request_id',
        'pris_en_charge_le',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'conseiller_id' => 'integer',
        'montant' => 'integer',
        'duree' => 'integer',
        'rendez_vous' => 'array',
        'pris_en_charge_le' => 'datetime',
    ];

    public function usager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conseiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conseiller_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(NoteDemande::class)->oldest();
    }

    /** Demandes encore à prendre en charge par ce conseiller : celles de sa zone, et celles qui lui sont adressées. */
    public function scopeAPrendreEnCharge(Builder $query, User $conseiller): Builder
    {
        return $query->where('statut', self::NOUVELLE)->where(function (Builder $q) use ($conseiller) {
            $q->where('conseiller_id', $conseiller->id);
            if ($conseiller->zone) {
                $q->orWhere(fn (Builder $z) => $z->whereNull('conseiller_id')->where('zone', $conseiller->zone));
            }
        });
    }

    /** Dossiers suivis par ce conseiller, une fois pris en charge. */
    public function scopeSuiviesPar(Builder $query, User $conseiller): Builder
    {
        return $query->where('conseiller_id', $conseiller->id)->where('statut', '!=', self::NOUVELLE);
    }
}
