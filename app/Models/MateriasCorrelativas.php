<?php

namespace App\Models;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Model;

class MateriasCorrelativas extends Model
{
    protected $table = 'materias_correlativas';
    public $timestamps = false;

    protected $fillable = [
        'id_materia',
        'id_correlativa',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function correlativa()
    {
        return $this->belongsTo(Materia::class, 'id_correlativa');
    }
}