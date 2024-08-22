<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapeProcess extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'codeEtape',
        'libelleEtape'
    ];

    public function processFabrication()
    {
        return $this->hasMany(ProcessFabrication::class, 'codeEtape', 'codeEtape');
    }

}
