<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    protected $table = 'Concepto';
    protected $primaryKey = 'id_concepto';
    protected $fillable = ['nombre'];
    public $timestamps = false;
    
    public function boletas()
    {
        return $this->hasMany(Precio::class, 'id_concepto');
    }
}
