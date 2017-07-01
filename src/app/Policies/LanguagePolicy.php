<?php

namespace LaravelEnso\Localisation\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Localisation\app\Models\Language;

class LanguagePolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Language $language)
    {
        return $this->isNotDefault($language)
            && $this->isNotUserLocale($user, $language);
    }

    private function isNotDefault(Language $language)
    {
        if ($language->name === config('app.fallback_locale')) {
            throw new \EnsoException(__("You can't remove the fallback locale"));
        }

        return true;
    }

    private function isNotUserLocale(User $user, Language $language)
    {
        if ($language->name === $user->getLanguage()) {
            throw new \EnsoException(__("You can't remove the language that you are currently using"));
        }

        return true;
    }
}
