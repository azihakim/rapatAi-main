<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLogistik extends Model
{
    protected $guarded = [];

    public function logistik()
    {
        return $this->belongsTo(PengajuanLogistik::class, 'pengajuan_logistik_id');
    }
}
