<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    protected $fillable = [
        'rapat_id',
        'isi',
        'file_path',
        'status',
    ];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }
}
