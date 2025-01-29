<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'court_id',
        'day_of_week',
        'start_time',
        'end_time',
        'blocked',
    ];

    /**
     * Relacionamento com a quadra.
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
