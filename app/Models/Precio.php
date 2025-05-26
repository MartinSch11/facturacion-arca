<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'Precios';
    protected $primaryKey = 'id_precio';
    public $timestamps = false;
    protected $fillable = ['id_carrera', 'precio'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }
}
