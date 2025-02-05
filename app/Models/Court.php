<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'zip_code',
        'street',
        'number',
        'logradouro',
        'complemento',
        'bairro',
        'localidade',
        'estado',
        'coordinate_x',
        'coordinate_y',
        'price_per_hour',
        'user_id',
        'initial_hour',
        'final_hour',
        'work_days'
    ];

    /**
     * Get the user that owns the court.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sports for the court.
     */
    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class);
    }

    /**
     * Get the gallery photos for the court.
     */
    public function photos(): HasMany
    {
        return $this->HasMany(GalleryPhoto::class);
    }

    protected static function booted () {
        static::deleting(function(Court $court) {
            $court->photos()->delete();
        });
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'court_id', 'user_id')->withTimestamps();
    }

    public function bookingsBy()
    {
        return $this->belongsToMany(User::class, 'bookings', 'court_id', 'user_id')->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(CourtSchedule::class);
    }

}
