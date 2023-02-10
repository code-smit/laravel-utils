<?php

namespace Jomo\Utils;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        $creationCallback = function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        };

        static::creating($creationCallback);
    }
}
