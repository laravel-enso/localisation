<?php

namespace LaravelEnso\Localisation\app\Exceptions;

use LaravelEnso\Helpers\app\Exceptions\EnsoException;

class Localisation extends EnsoException
{
    public static function folderExists($locale, $folder)
    {
        return new self(__(
            "Can't create the language for locale :locale files because the legacy folder :folder already exists",
            ['locale' => $locale, 'folder' => $folder]
        ));
    }
}
