<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Location Model
 *
 * @property string $name
 * @property string $lat
 * @property string $lon
 */
class Location extends Model
{
    protected $fillable = ['province_id', 'name', 'lat', 'lon'];

    /**
     * Get the province that owns the location.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
