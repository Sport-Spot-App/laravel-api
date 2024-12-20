<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'coordinate_x',
        'coordinate_y',
        'price_per_hour',
        'user_id',
    ];

    /**
     * Get the user that owns the court.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
