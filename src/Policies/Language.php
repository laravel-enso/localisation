<?php

namespace LaravelEnso\Localisation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use LaravelEnso\Localisation\Models\Language as Model;
use LaravelEnso\Users\Models\User;

class Language
{
    use HandlesAuthorization;

    public function destroy(User $user, Model $language): Response
    {
        if ($this->isDefault($language)) {
            return Response::deny(__('You cannot delete the default language'));
        }

        if ($this->isCurrentLocale($user, $language)) {
            return Response::deny(__('You cannot delete the current language'));
        }

        return Response::allow();
    }

    private function isDefault(Model $language)
    {
        return $language->name === config('app.fallback_locale');
    }

    private function isCurrentLocale(User $user, Model $language)
    {
        return $language->name === $user->preferences->lang();
    }
}
