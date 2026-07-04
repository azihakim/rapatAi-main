<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalRapatHari extends Model
{
    protected $guarded = [];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }
}
