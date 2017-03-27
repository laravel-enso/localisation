<?php

namespace LaravelEnso\Localisation\app\Validation;

use LaravelEnso\Localisation\app\Traits\Logger;

class ValidateNewTranslation
{
    use Logger;

    public function isProcessable($translation)
    {
        $isProcessable = strpos($translation, '$') === false;

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label has unknown format');
        }

        return $isProcessable;
    }
}
