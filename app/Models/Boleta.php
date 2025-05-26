<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    protected $table = 'Boleta';
    protected $primaryKey = 'nro_boleta';
    public $timestamps = false;

    protected $fillable = [
        'matricula',
        'estado',
        'fecha_pago',
        'importe_pagado',
        'id_emision',
        'id_concepto',
        'nombre_detallado',
    ];

    // Relación con CarreraXAlumno
    public function carreraXAlumno()
    {
        return $this->belongsTo(CarreraXAlumno::class, 'matricula', 'matricula');
    }

    // Relación con EmisionBoleta
    public function emisionBoleta()
    {
        return $this->belongsTo(EmisionBoleta::class, 'id_emision');
    }

    // Relación con DetalleBoleta
    public function detalles()
    {
        return $this->hasMany(DetalleBoleta::class, 'nro_boleta');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto');
    }

    public function alumno()
    {
        return $this->belongsTo(\App\Models\User::class, 'dni_alumno', 'dni');
    }

}
