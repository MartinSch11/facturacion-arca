<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'Carrera';
    protected $primaryKey = 'id_carrera';
    protected $fillable = [
        'nombre',
        'duracion_anios'
    ];
    public $timestamps = false;

    // Relación con CarreraXAlumno
    public function carreraXAlumnos()
    {
        return $this->hasMany(CarreraXAlumno::class, 'id_carrera');
    }

    public function precio()
    {
        return $this->hasOne(Precio::class, 'id_precio');
    }

    public function planesEstudio()
    {
        return $this->hasMany(PlanEstudio::class, 'id_carrera');
    }

}
