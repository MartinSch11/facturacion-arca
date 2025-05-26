<?

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleBoleta extends Model
{
    protected $table = 'DetalleBoleta';
    protected $primaryKey = 'id_detalle';
    protected $fillable = ['nro_boleta', 'concepto', 'precio'];

    // Relación con Boleta
    public function boleta()
    {
        return $this->belongsTo(Boleta::class, 'nro_boleta');
    }

    public function detallesBoleta()
    {
        return $this->hasMany(DetalleBoleta::class, 'id_concepto');
    }
}
