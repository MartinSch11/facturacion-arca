<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $primaryKey = 'id_config';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'punto_venta',
        'habilitar_inscripcion_comisiones',
        'fecha_inicio_inscripcion_1c',
        'fecha_inicio_inscripcion_2c',
    ];

    public function periodoActual(): ?string
    {
        $hoy = \Carbon\Carbon::today();

        if ($this->fecha_inicio_inscripcion_2c && $hoy->gte($this->fecha_inicio_inscripcion_2c)) {
            return '2C';
        }

        if ($this->fecha_inicio_inscripcion_1c && $hoy->gte($this->fecha_inicio_inscripcion_1c)) {
            return '1C';
        }

        return null;
    }

    // Helper para verificar habilitación
    public static function inscripcionHabilitada(): bool
    {
        return static::first()?->habilitar_inscripcion_comisiones ?? false;
    }
}
