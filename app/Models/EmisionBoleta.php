<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmisionBoleta extends Model
{
    protected $table = 'EmisionBoleta';
    protected $primaryKey = 'id_emision';
    public $timestamps = false; // Add if timestamps are not used
    protected $fillable = [
        'fecha_primer_vencimiento',
        'fecha_segundo_vencimiento',
        'fecha_tercer_vencimiento',
        'boleta_inicial',
        'boleta_final',
    ];

    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'id_emision');
    }
}
