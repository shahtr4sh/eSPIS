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
        'type',
        'thrust',
        'title',
        'prime_objective',
        'strategy',
        'indicator',
        'reference',
        'operational_definition',
        'data_source_id',
        'distribution_type',
        'status',
        'responsible_office_id',
        'baseline_value',
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
        'baseline_value' => 'decimal:2',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->code) {
                $model->dimension = substr($model->code, 0, 4);
            }
        });
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
}
