<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniteMesure extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'codeUnite',
        'libelleUnite'
    ];

    public function parametreAnalyse()
    {
        return $this->hasMany(ParametreAnalyse::class, 'codeunite', 'codeUnite');
    }
}
