<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiPi extends Model
{
    use SoftDeletes;

    protected $table = 'kpi_pis';

    protected $fillable = [
        'code',
        'dimension',
        'type',
        'thrust',
        'title',
        'prime_objective',
        'strategy',
        'indicator',
        'reference',
        'operational_definition',
        'data_source_id',
        'status',
        'responsible_office_id',
        'annual_target',
        'measurement',
        'attachment_path',
        'created_by',
        'updated_by',
        'sasaran_q1',
        'pencapaian_q1',
        'sasaran_q2',
        'pencapaian_q2',
        'sasaran_q3',
        'pencapaian_q3',
        'sasaran_q4',
        'pencapaian_q4',
        'sasaran_tahunan',
        'pencapaian_tahunan',
    ];

    protected $casts = [
        'annual_target' => 'decimal:2',
        'sasaran_q1' => 'decimal:2',
        'pencapaian_q1' => 'decimal:2',
        'sasaran_q2' => 'decimal:2',
        'pencapaian_q2' => 'decimal:2',
        'sasaran_q3' => 'decimal:2',
        'pencapaian_q3' => 'decimal:2',
        'sasaran_q4' => 'decimal:2',
        'pencapaian_q4' => 'decimal:2',
        'sasaran_tahunan' => 'decimal:2',
        'pencapaian_tahunan' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function dataSource()
    {
        return $this->belongsTo(DataSource::class, 'data_source_id');
    }

    public function responsibleOffice()
    {
        return $this->belongsTo(ResponsibleOffice::class, 'responsible_office_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function distributionWeights()
    {
        return $this->hasMany(KpiPiDistributionWeight::class, 'kpi_pi_id');
    }

    public function distributionQuarterAchievements()
    {
        return $this->hasMany(KpiPiDistributionQuarterAchievement::class, 'kpi_pi_id');
    }

    public function getTotalWeightAttribute(): float
    {
        return (float) $this->distributionWeights()->sum('weight_value');
    }
}
