<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';

    protected $fillable = [
        'id_cursada',
        'tipo',
        'valor',
        'fecha',
        'observaciones',
    ];

    public $timestamps = false;

    /**
     * Relación: una nota pertenece a una cursada.
     */
    public function cursada()
    {
        return $this->belongsTo(Cursada::class, 'id_cursada');
    }
}
