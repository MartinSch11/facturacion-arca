<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumnos extends Model
{
    protected $table = 'Alumnos';
    protected $primaryKey = 'dni';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'cuit',
        'direccion',
        'province_id',
        'location_id',
    ];

    // Relación con CarreraXAlumno
    public function carreraXAlumno()
    {
        return $this->hasOne(CarreraXAlumno::class, 'dni_alumno', 'dni');
    }

    // Relación con Facturas
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'matricula');
    }

    // Relación con User
    public function user()
    {
        return $this->hasOne(User::class, 'dni', 'dni');
    }

    // En App\Models\Alumnos.php

    public function provincia()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function localidad()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function cursadas()
    {
        return $this->hasMany(Cursada::class, 'dni_alumno', 'dni');
    }
    public function materiasAprobadas()
    {
        return $this->cursadas()->where('estado', 'Aprobado');
    }

    /**
     * Calcula el año de carrera actual del alumno según materias aprobadas.
     * Retorna 1 si no aprobó ninguna, 2 si aprobó todas las de 1er año, etc.
     */
    public function anioCarreraActual()
    {
        $carrera = $this->carreraXAlumno?->carrera;
        if (!$carrera)
            return 1;

        $plan = $carrera->planesEstudio()->first();
        if (!$plan)
            return 1;

        $materiasPorAnio = $plan->materias()
            ->get()
            ->groupBy('anio');

        $aprobadas = $this->materiasAprobadas()->pluck('id_materia')->toArray();

        $anioActual = 1;
        foreach ($materiasPorAnio as $anio => $materias) {
            $todasAprobadas = $materias->every(function ($materia) use ($aprobadas) {
                return in_array($materia->id, $aprobadas);
            });
            if ($todasAprobadas) {
                $anioActual = $anio + 1;
            } else {
                break;
            }
        }
        return $anioActual;
    }

    /**
     * Actualiza el campo anio_actual en CarreraXAlumno según materias aprobadas.
     */
    public function actualizarAnioActual()
    {
        $anio = $this->anioCarreraActual();
        if ($this->carreraXAlumno) {
            $this->carreraXAlumno->anio_actual = $anio;
            $this->carreraXAlumno->save();
        }
    }

    /**
     * Relación: un alumno puede tener muchas comisiones a través de sus cursadas.
     */
    public function comisiones()
    {
        return $this->hasManyThrough(ComisionMateria::class, Cursada::class, 'dni_alumno', 'id', 'dni', 'id_comision_materia');
    }

}
