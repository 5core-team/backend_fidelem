<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const USAGER = 'user';

    public const CONSEILLER = 'advisor';

    public const RESPONSABLE = 'manager';

    public const EN_ATTENTE = 'En attente';

    public const ACTIF = 'Actif';

    public const REJETE = 'Rejeté';

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'address',
        'email',
        'type_compte',
        'statut',
        'password',
        'created_by',
        'zone',
        'niveau',
        'financements',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_by' => 'integer',
        'financements' => 'array',
    ];

    /** Conseiller qui a créé le compte de cet usager. */
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Usagers créés par ce conseiller. */
    public function clients(): HasMany
    {
        return $this->hasMany(User::class, 'created_by')->where('type_compte', self::USAGER);
    }

    /** Demandes de financement de cet usager. */
    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeFinancement::class, 'user_id');
    }

    /** Demandes suivies par ce conseiller. */
    public function dossiers(): HasMany
    {
        return $this->hasMany(DemandeFinancement::class, 'conseiller_id');
    }

    public function estUsager(): bool
    {
        return $this->type_compte === self::USAGER;
    }

    public function estConseiller(): bool
    {
        return $this->type_compte === self::CONSEILLER;
    }

    public function estResponsable(): bool
    {
        return $this->type_compte === self::RESPONSABLE;
    }

    public function estActif(): bool
    {
        return $this->statut === self::ACTIF;
    }

    public function nomComplet(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    public function scopeConseillersActifs(Builder $query): Builder
    {
        return $query->where('type_compte', self::CONSEILLER)->where('statut', self::ACTIF);
    }
}
