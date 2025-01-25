<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'path', 'is_main','court_id'];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
