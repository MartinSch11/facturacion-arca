<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'Facturas';
    protected $primaryKey = 'nro_factura';
    public $timestamps = false;

    protected $fillable =
        [
            'tipo_factura',
            'matricula',
            'fecha_emision',
            'fecha_vencimiento',
            'importe_pagado',
            'fecha_pago',
            'cae',
            'cae_vencimiento',
            'forma_pago',
            'estado',
            'datos_para_afip',
        ];
    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    // Relación con CarreraXAlumno (a través de matrícula)
    public function carreraXAlumno()
    {
        return $this->belongsTo(CarreraXAlumno::class, 'matricula', 'matricula');
    }

    // Relación con DetalleFactura
    public function detalleFactura()
    {
        return $this->hasOne(DetalleFactura::class, 'nro_factura', 'nro_factura');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'nro_factura');
    }

    public function getFormaPagoLabelAttribute(): string
    {
        return match ($this->forma_pago) {
            'contado' => 'Contado',
            'tarjeta_credito' => 'Tarjeta de Crédito',
            'tarjeta_debito' => 'Tarjeta de Débito',
            default => ucwords(str_replace('_', ' ', $this->forma_pago)),
        };
    }
    

}

