<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiPi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'thrust',
        'prime_objective',
        'strategy',
        'dimension',
        'title',
        'reference',
        'operational_definition',
        'data_source_id',
        'distribution_type',
        'responsible_office_id',
        'baseline_value',
        'annual_target',
        'remarks',
        'status',
        'attachment_path',
        'created_by',
        'updated_by',
    ];

    public function office()
    {
        return $this->belongsTo(ResponsibleOffice::class, 'responsible_office_id');
    }

    public function dataSource()
    {
        return $this->belongsTo(DataSource::class, 'data_source_id');
    }

    public function achievements()
    {
        return $this->hasMany(KpiPiAchievement::class);
    }
}
