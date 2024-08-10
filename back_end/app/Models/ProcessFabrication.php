<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessFabrication extends Model
{
    use HasFactory, Compoships;

    protected $primaryKey = ['codePlan', 'codeEtape'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'orderEtape',
        'codeEtape',
        'codePlan',
    ];

    public function planControle()
    {
        return $this->belongsTo(PlanControle::class, 'codePlan', 'codePlan');
    }

    public function etapeProcess()
    {
        return $this->belongsTo(EtapeProcess::class, 'codeEtape', 'codeEtape');
    }

    public function analyseProcess()
    {
        return $this->hasMany(AnalyseProcess::class, ['codeEtape', 'codePlan'], ['codeEtape', 'codePlan']);
    }
    // Override the method to get the primary key
    public function getKey()
    {
        return $this->getAttribute($this->primaryKey);
    }

    // Override the method to get the key type
    public function getKeyType()
    {
        return 'array';
    }

    // Override the method to check if the key is incrementing
    public function getIncrementing()
    {
        return false;
    }

    // Override the method to set the primary key value
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }
}
