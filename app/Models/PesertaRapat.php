<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaRapat extends Model
{
    protected $fillable = [
        'rapat_id',
        'user_id',
    ];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
