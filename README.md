# Localisation Manager
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/235db862227e460792a72a1e65427d1f)](https://www.codacy.com/app/laravel-enso/Localisation?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/Localisation&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85617309/shield?branch=master)](https://styleci.io/repos/85617309)
[![Total Downloads](https://poser.pugx.org/laravel-enso/localisation/downloads)](https://packagist.org/packages/laravel-enso/localisation)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/localisation/version)](https://packagist.org/packages/laravel-enso/localisation)

Localisation management dependency for [Laravel Enso](https://github.com/laravel-enso/Enso).

![Screenshot](https://laravel-enso.github.io/localisation/screenshots/Selection_010.png)

![Screenshot](https://laravel-enso.github.io/localisation/screenshots/Selection_011.png)


### Details

- allows an easier management of languages, keys and translations in the context of a multi language application
- uses the newer Laravel `__()` translation method and stores keys and translations inside a json file
- permits adding as many languages as you require and the easy creation of translations for the keys you need
- a language selector VueJS component is included, that can be used to set the current language for the active user

### Publishes

- `php artisan vendor:publish --tag=localisation-component` - the VueJS component 
- `php artisan vendor:publish --tag=enso-update` - a common alias for when wanting to update the VueJS component, 
once a newer version is released

### Notes

The [Laravel Enso Core](https://github.com/laravel-enso/Core) package comes with this package included.

### TO DO

- [ ] sync-json command / button

### Contributions

are welcome