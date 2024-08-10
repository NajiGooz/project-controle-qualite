<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanControle extends Model
{
    use HasFactory;

    protected $fillable = [
        'codePlan',
        'etatPlan',
        'codeProduit',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'codeProduit', 'codeProduit');
    }
    public function ProcessFabrication()
    {
        return $this->hasMany(ProcessFabrication::class , 'codePlan', 'codePlan');
    }
}
