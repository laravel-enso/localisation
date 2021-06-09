<?php

namespace LaravelEnso\Localisation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Localisation\Models\Language as Model;
use LaravelEnso\Users\Models\User;

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
