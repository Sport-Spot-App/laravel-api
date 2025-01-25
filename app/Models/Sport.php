<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name','photo'];

    public function courts(): BelongsToMany
    {
        return $this->belongsToMany(Court::class);
    }
}
