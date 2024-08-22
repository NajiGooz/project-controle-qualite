<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'codeProduit',
        'libelleProduit',
    ];

    public function planContole()
    {
        return $this->hasOne(PlanControle::class, 'codeProduit', 'codeProduit');
    }
}
