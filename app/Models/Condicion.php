<?php

namespace App\Models;

use App\Models\CarreraXAlumno;
use Illuminate\Database\Eloquent\Model;

class Condicion extends Model
{
    protected $table = 'Condiciones';
    protected $primaryKey = 'id_condicion';
    protected $fillable = ['nombre'];
    public $timestamps = false;

    public function carreraXAlumnos()
    {
        return $this->hasMany(CarreraXAlumno::class, 'id_condicion');
    }
}
