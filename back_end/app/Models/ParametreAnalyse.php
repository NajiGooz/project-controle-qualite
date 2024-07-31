<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametreAnalyse extends Model
{
    use HasFactory;

    protected $fillable = [
        'codeParam',
        'libelleParam',
        'codeAnalyse',
        'codeUnite',
    ];

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }
    public function uniUniteMesurete()
    {
        return $this->belongsTo(UniteMesure::class);
    }

    public function parametreAnalyseProcess()
    {
        return $this->hasMany(ParametreAnalyseProcess::class);
    }
}
