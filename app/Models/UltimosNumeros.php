<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UltimosNumeros extends Model
{
    protected $table = 'ultimos_numeros';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'punto_venta',
        'ultimo_numero',
    ];
}
