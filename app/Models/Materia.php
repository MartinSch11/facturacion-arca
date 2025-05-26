<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_plan_estudio',
        'anio',
        'modalidad',
        'cupo',
    ];

    public function planEstudio()
    {
        return $this->belongsTo(PlanEstudio::class, 'id_plan_estudio');
    }

    public function correlativas()
    {
        return $this->belongsToMany(
            Materia::class,
            'materias_correlativas',
            'id_materia',
            'id_correlativa'
        );
    }
}