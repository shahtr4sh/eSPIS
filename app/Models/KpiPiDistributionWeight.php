<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiPiDistributionWeight extends Model
{
    protected $table = 'kpi_pi_distribution_weights';

    protected $fillable = [
        'kpi_pi_id',
        'distribution_unit_id',
        'weight_value',
    ];

    protected $casts = [
        'weight_value' => 'decimal:2',
    ];

    public function kpiPi()
    {
        return $this->belongsTo(KpiPi::class, 'kpi_pi_id');
    }

    public function distributionUnit()
    {
        return $this->belongsTo(DistributionUnit::class, 'distribution_unit_id');
    }
}
