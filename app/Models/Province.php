<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Province Model
 *
 * @property string $name
 * @property string $lat
 * @property string $lon
 */
class Province extends Model
{
    protected $fillable = ['name', 'lat', 'lon'];

    /**
     * Get the locations for the province.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
