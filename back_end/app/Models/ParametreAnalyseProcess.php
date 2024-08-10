<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametreAnalyseProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'codePAP',
        'valeurMin',
        'valeurMax',
        'codeParam',
        'codeAP',
    ];
    protected $primaryKey = 'codePAP';
    protected $keyType = 'string';

    public $incrementing = false;


    public function analyseProcess()
    {
        return $this->belongsTo(AnalyseProcess::class, 'codeAP', 'codeAP');
    }

    public function parametreAnalyse()
    {
        return $this->belongsTo(ParametreAnalyse::class, 'codeParam', 'codeParam');
    }
}
