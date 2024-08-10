<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyseProcess extends Model
{
    use HasFactory, Compoships;

    protected $fillable = [
        'codeAP',
        'codeAnalyse',
        'codeEtape',
        'codePlan',
    ];

    public function analyse()
    {
        return $this->belongsTo(Analyse::class, 'codeAnalyse', 'codeAnalyse');
    }
    public function processFabrication()
    {
        return $this->belongsTo(ProcessFabrication::class, ['codeEtape', 'codePlan'], ['codeEtape', 'codePlan']);
    }

    public function parametreAnalyseProcess()
    {
        return $this->hasMany(ParametreAnalyseProcess::class, 'codeAP', 'codeAP');
    }
}
