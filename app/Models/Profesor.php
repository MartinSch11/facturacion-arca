<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $table = 'profesores';
    protected $primaryKey = 'profesor_id';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'titulo',
    ];

    protected $appends = ['nombre_completo'];

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(
            fn () => "{$this->nombre} {$this->apellido}"
        );
    }
}
