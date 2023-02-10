<?php

namespace Jomo\Utils;

use Illuminate\Database\Eloquent\Model;
use Malhal\Geographical\Geographical;

trait HasLocation
{
    use Geographical;

    protected static $kilometers = true;

    public function scopeWithinRadius($query, Model $model, int $radius)
    {
        return $query->distance($model->latitude, $model->longitude)->havingRaw("{$radius} >= distance");
    }
}
