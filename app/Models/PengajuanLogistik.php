<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanLogistik extends Model
{
    protected $guarded = [];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class, 'rapat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailLogistik::class, 'pengajuan_logistik_id');
    }
}
