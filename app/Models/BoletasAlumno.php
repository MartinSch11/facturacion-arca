<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoletasAlumno extends Model
{
    protected $table = 'Boleta';
    protected $primaryKey = 'nro_boleta';
    public $timestamps = false;

    // Campos que se pueden llenar
    protected $fillable = [
        'matricula',
        'estado',
        'fecha_pago',
        'importe_pagado',
        'id_emision',
        'id_concepto',
        'nombre_detallado',
    ];

    public function alumno()
    {
        return $this->belongsTo(User::class, 'dni_alumno', 'dni');
    }

    public function carreraXAlumno()
    {
        return $this->belongsTo(CarreraXAlumno::class, 'matricula', 'matricula');
    }

    public function emisionBoleta()
    {
        return $this->belongsTo(EmisionBoleta::class, 'id_emision', 'id_emision');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function getRouteKeyName(): string
    {
        return 'nro_boleta';
    }

    public function boleta()
{
    return $this->belongsTo(Boleta::class, 'nro_boleta', 'nro_boleta');
}

    public function detalleFactura()
    {
        // asumo que DetalleFactura tiene columna `nro_boleta` y `nro_factura`
        return $this->hasOne(DetalleFactura::class, 'nro_boleta', 'nro_boleta');
    }
}
