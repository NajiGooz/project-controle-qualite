<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'codeAnalyse',
        'libelleAnalyse'
    ];

    public function parametreAnalyse()
    {
        return $this->hasMany(ParametreAnalyse::class, 'codeAnalyse', 'codeAnalyse');
    }
}
