<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'Detalle_Factura'; // Nombre ajustado
    protected $primaryKey = 'id_detalle';

    protected $fillable = ['nro_boleta', 'nro_factura'];
    public $timestamps = false; // Desactiva las marcas de tiempo

    // Relación con Factura
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'nro_factura', 'nro_factura');
    }

    // Relación con Boleta
    public function boleta()
    {
        return $this->belongsTo(Boleta::class, 'nro_boleta', 'nro_boleta');
    }
}
