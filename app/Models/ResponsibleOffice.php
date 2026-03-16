<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsibleOffice extends Model
{
    protected $table = 'responsible_offices';

    protected $fillable = [
        'name',
    ];

    public function kpiPis()
    {
        return $this->hasMany(KpiPi::class, 'responsible_office_id');
    }
}
