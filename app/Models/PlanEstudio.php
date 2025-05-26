<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEstudio extends Model
{
    protected $table = 'planes_estudio';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_carrera',
        'anio_implementacion',
    ];

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_plan_estudio');
    }
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    public function materiasCorrelativas()
    {
        return $this->hasManyThrough(
            \App\Models\MateriasCorrelativas::class,
            \App\Models\Materia::class,
            'id_plan_estudio', // Foreign key on Materia
            'id_materia',      // Foreign key on MateriasCorrelativas
            'id',              // Local key on PlanEstudio
            'id'               // Local key on Materia
        );
    }
}