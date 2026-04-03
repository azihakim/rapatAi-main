<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjutRapat extends Model
{
    //

    protected $guarded = [];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
