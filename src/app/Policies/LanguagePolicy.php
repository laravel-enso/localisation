<?php

namespace LaravelEnso\Localisation\app\Policies;

use LaravelEnso\Core\app\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
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
        return $language->name !== config('app.fallback_locale');
    }

    private function isNotUserLocale(User $user, Language $language)
    {
        return $language->name !== $user->preferences->global->lang;
    }
}
