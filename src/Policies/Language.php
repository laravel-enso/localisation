<?php

namespace LaravelEnso\Localisation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Core\Models\User;
use LaravelEnso\Localisation\Models\Language as Model;

class Language
{
    use HandlesAuthorization;

    public function destroy(User $user, Model $language)
    {
        return $this->isNotDefault($language)
            && $this->isNotUserLocale($user, $language);
    }

    private function isNotDefault(Model $language)
    {
        return $language->name !== config('app.fallback_locale');
    }

    private function isNotUserLocale(User $user, Model $language)
    {
        return $language->name !== $user->lang();
    }
}
