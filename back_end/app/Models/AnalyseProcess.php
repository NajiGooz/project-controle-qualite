<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyseProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'codeAP',
        'codeAnalyse',
        'codeEtape',
        'codePlan',
    ];

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }
    public function processFabrication()
    {
        return $this->belongsTo(ProcessFabrication::class, ['codeEtape', 'codePlan']);
    }
}
