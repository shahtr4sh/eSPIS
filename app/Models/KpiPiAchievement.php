<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiPiAchievement extends Model
{
    protected $fillable = [
        'kpi_pi_id',
        'year',
        'quarter',
        'quarter_target',
        'actual_value',
        'achievement_percentage',
        'achievement_date',
        'remarks',
        'evidence_path',
    ];

    public function kpiPi()
    {
        return $this->belongsTo(KpiPi::class);
    }
}
