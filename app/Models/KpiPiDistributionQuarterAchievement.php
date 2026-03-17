<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiPiDistributionQuarterAchievement extends Model
{
    protected $table = 'kpi_pi_distribution_quarter_achievements';

    protected $fillable = [
        'kpi_pi_id',
        'distribution_unit_id',
        'quarter',
        'achievement_value',
    ];

    protected $casts = [
        'achievement_value' => 'decimal:2',
    ];

    public function kpiPi()
    {
        return $this->belongsTo(KpiPi::class, 'kpi_pi_id');
    }

    public function distributionUnit()
    {
        return $this->belongsTo(DistributionUnit::class, 'distribution_unit_id');
    }

    public function getQuarterLabelAttribute(): string
    {
        return match ($this->quarter) {
            'Q1' => 'Quarter 1',
            'Q2' => 'Quarter 2',
            'Q3' => 'Quarter 3',
            'Q4' => 'Quarter 4',
            default => (string) $this->quarter,
        };
    }
}
