<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionMateria extends Model
{
    protected $table = 'inscripcion_materia';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'dni_alumno',
        'id_comision_materia',
        'fecha_inscripcion',
    ];

    // Relación con Alumno
    public function alumno()
    {
        return $this->belongsTo(Alumnos::class, 'dni_alumno', 'dni');
    }

    // Relación con ComisionMateria
    public function comisionMateria()
    {
        return $this->belongsTo(ComisionMateria::class, 'id_comision_materia', 'id');
    }
}