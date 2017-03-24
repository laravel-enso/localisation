<?php

namespace LaravelEnso\Localisation\App\Validation;

use LaravelEnso\Localisation\App\Traits\Logger;

class ValidateLegacyTranslation
{
    use Logger;

    public function isProcessable($translation)
    {
        $isProcessable = $this->checkTranslationQuotes($translation)
        && $this->checkTranslationLength($translation)
        && $this->checkTranslationForVariables($translation)
        && $this->checkTranslationForConcat($translation);

        return $isProcessable;
    }

    private function checkTranslationQuotes($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $isProcessable = $label[0] === $label[strlen($label) - 1] && ($label[0] === '"' || $label[0] === "'");

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label has unknown format');
        }

        return $isProcessable;
    }

    private function checkTranslationLength($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $isProcessable = strlen($label) > 3;

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label is too short');
        }

        return $isProcessable;
    }

    private function checkTranslationForVariables($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $isProcessable = $label[0] === '"' ? (strpos($label, '$') === false ? true : false) : true;

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label may contain variables');
        }

        return $isProcessable;
    }

    private function checkTranslationForConcat($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 2, strlen($label) - 4);
        $isProcessable = strpos($label, "'") === false && strpos($label, '"') === false;

        if (!$isProcessable) {
            $this->logError($translation.' => '.'The label has an unknown format');
        }

        return $isProcessable;
    }

    private function logError($error)
    {
        \Log::error($error);
    }
}
