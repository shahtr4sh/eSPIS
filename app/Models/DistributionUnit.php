<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionUnit extends Model
{
    protected $table = 'distribution_units';

    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function distributionWeights()
    {
        return $this->hasMany(KpiPiDistributionWeight::class, 'distribution_unit_id');
    }

    public function distributionQuarterAchievements()
    {
        return $this->hasMany(KpiPiDistributionQuarterAchievement::class, 'distribution_unit_id');
    }
}
