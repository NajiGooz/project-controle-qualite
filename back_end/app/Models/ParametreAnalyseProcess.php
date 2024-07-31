<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametreAnalyseProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'valeurMin',
        'valeurMax',
        'codePara',
        'codeAP',
    ];

    public function analyseProcess()
    {
        return $this->belongsTo(AnalyseProcess::class);
    }

    public function parametreAnalyse()
    {
        return $this->belongsTo(ParametreAnalyse::class);
    }

}
