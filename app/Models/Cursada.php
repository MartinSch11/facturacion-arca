<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cursada extends Model
{
    protected $table = 'cursadas';

    protected $fillable = [
        'dni_alumno',
        'id_materia',
        'id_comision_materia',
        'anio',
        'cuatrimestre',
        'estado',
        'nota_1erParcial',
        'nota_2doParcial',
        'nota_final',
        'fecha_inscripcion',
    ];

    public $timestamps = false;

    // Relación: una cursada pertenece a un alumno
    public function alumno()
    {
        return $this->belongsTo(Alumnos::class, 'dni_alumno', 'dni');
    }

    // Relación: una cursada pertenece a una materia
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    // Relación: una cursada pertenece a una comisión-materia
    public function comisionMateria()
    {
        return $this->belongsTo(ComisionMateria::class, 'id_comision_materia', 'id');
    }

    // Relación: una cursada tiene muchas notas
    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_cursada');
    }

    protected static function booted()
    {
        static::updated(function ($cursada) {
            // Si la nota_final es mayor o igual a 6 y el estado es 'Cursando', pasa a 'Aprobado'
            if (
                $cursada->nota_final !== null
                && $cursada->nota_final >= 6
                && $cursada->estado === 'Cursando'
            ) {
                $cursada->estado = 'Aprobado';
                $cursada->save();
                // El trigger de 'Aprobado' actualizará el año del alumno
                return;
            }

            // Si la cursada fue aprobada, actualiza el año actual del alumno
            if ($cursada->estado === 'Aprobado') {
                $alumno = $cursada->alumno;
                if ($alumno) {
                    $alumno->actualizarAnioActual();
                }
            }
        });
    }
}
