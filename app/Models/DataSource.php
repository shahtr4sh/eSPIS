<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    protected $table = 'data_sources';

    protected $fillable = [
        'name',
    ];

    public function kpiPis()
    {
        return $this->hasMany(KpiPi::class, 'data_source_id');
    }
}
