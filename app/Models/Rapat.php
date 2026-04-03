<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapat extends Model
{
    protected $guarded = [];

    public function pesertaRapat()
    {
        return $this->hasMany(PesertaRapat::class);
    }
}
