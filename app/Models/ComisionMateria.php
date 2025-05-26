<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComisionMateria extends Model
{
    protected $table = 'comisiones_materias';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_materia',
        'profesor_id',
        'turno',
        'division',
        'cupo',
    ];

    // Relación con Materia
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function cursadas()
    {
        return $this->hasMany(Cursada::class, 'id_comision_materia');
    }

}