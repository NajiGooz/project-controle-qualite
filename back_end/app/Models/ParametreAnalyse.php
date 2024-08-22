<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametreAnalyse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'codeParam',
        'libelleParam',
        'codeAnalyse',
        'codeUnite',
    ];

    public function analyse()
    {
        return $this->belongsTo(Analyse::class, 'codeAnalyse', 'codeAnalyse');
    }
    public function uniteMesure()
    {
        return $this->belongsTo(UniteMesure::class, 'codeUnite', 'codeUnite');
    }

    public function parametreAnalyseProcess()
    {
        return $this->hasMany(ParametreAnalyseProcess::class, 'codeParam', 'codeParam');
    }
}
