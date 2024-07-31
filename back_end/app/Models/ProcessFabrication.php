<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessFabrication extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderEtape',
        'codeEtape',
        'codePlan',
    ];

    public function planControle()
    {
        return $this->belongsTo(PlanControle::class);
    }

    public function etapeProcess()
    {
        return $this->belongsTo(EtapeProcess::class);
    }
}
