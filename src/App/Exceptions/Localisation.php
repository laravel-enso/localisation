<?php

namespace LaravelEnso\Localisation\App\Exceptions;

use LaravelEnso\Helpers\App\Exceptions\EnsoException;

class Localisation extends EnsoException
{
    public static function legacyFolderExists($locale, $folder)
    {
        return new self(__(
            "Can't create the language for locale :locale. The legacy folder :folder already exists",
            ['locale' => $locale, 'folder' => $folder]
        ));
    }
}
