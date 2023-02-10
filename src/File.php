<?php

namespace CodeSmit\LaravelHelpers;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;

class File implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if (!$value) {
            return null;
        }

        return Storage::url($value);
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
