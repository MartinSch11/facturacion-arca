<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraXAlumno extends Model
{
    protected $table = 'CarreraXAlumno';
    protected $primaryKey = ['dni_alumno', 'id_carrera'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'dni_alumno',
        'id_carrera',
        'matricula',
        'id_condicion'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumnos::class, 'dni_alumno', 'dni');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    public function condicion()
    {
        return $this->belongsTo(Condicion::class, 'id_condicion');
    }

    public static function generateMatricula($dni, $id_carrera)
    {
        $lastRecord = self::where('id_carrera', $id_carrera)
            ->orderBy('matricula', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->matricula, strlen((string) $id_carrera));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        return $id_carrera . $formattedNumber;
    }
}
