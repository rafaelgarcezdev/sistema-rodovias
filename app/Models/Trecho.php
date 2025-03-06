<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trecho extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_referencia',
        'uf_id',
        'rodovia_id',
        'quilometragem_inicial',
        'quilometragem_final',
        'tipo',
        'geo',
    ];

    protected $casts = [
        'geo' => 'array',
    ];

  
    public function uf()
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }

    
    public function rodovia()
    {
        return $this->belongsTo(Rodovia::class, 'rodovia_id');
    }
}
