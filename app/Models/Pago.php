<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'Pagos';
    protected $primaryKey = 'id_pago';

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }
}
