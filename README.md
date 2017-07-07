<!--h-->
# Localisation Manager
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/235db862227e460792a72a1e65427d1f)](https://www.codacy.com/app/laravel-enso/Localisation?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/Localisation&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85617309/shield?branch=master)](https://styleci.io/repos/85617309)
[![License](https://poser.pugx.org/laravel-enso/localisation/license)](https://https://packagist.org/packages/laravel-enso/localisation)
[![Total Downloads](https://poser.pugx.org/laravel-enso/localisation/downloads)](https://packagist.org/packages/laravel-enso/localisation)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/localisation/version)](https://packagist.org/packages/laravel-enso/localisation)
<!--/h-->

Localisation management dependency for [Laravel Enso](https://github.com/laravel-enso/Enso).

[![Screenshot](https://laravel-enso.github.io/localisation/screenshots/Selection_010_thumb.png)](https://laravel-enso.github.io/localisation/screenshots/Selection_010.png)

[![Watch the demo](https://laravel-enso.github.io/localisation/screenshots/Selection_011_thumb.png)](https://laravel-enso.github.io/localisation/videos/demo_01.webm)
<sup>click on the photo to view a short demo in compatible browsers</sup>

### Features

- allows an easier management of languages, keys and translations in the context of a multi language application
- uses the newer Laravel `__()` translation method and stores keys and translations inside a json file
- permits adding as many languages as you require and the easy creation of translations for the keys you need
- a language selector VueJS component is included, that can be used to set the current language for the active user

### Under the Hood

- the `languages` table stores the available languages for localisation
   - `name` - the language code, e.g. 'en'
   - `display_name` - the lable for the language, visible in the UI, e.g. 'English'
   - `flag` - the icon class used for showing the flag

- when translating, the new Laravel mechanism is used, respectively the function `__()`
- the main language is considered to be english
- the keys are, by convention, in english and in a human readable format e.g. 'Date of Birth', and if a key is not found, the value of the key is used instead
- the keys and the values for the keys are kept in `resources/lang/*code*.json`  where code is the language code, e.g. 'de' for german, with the exception for the english language, since keys are already in english
- due to Laravel's implementation, there are 4 translation categories which cannot be implemented using the new mechanism: `auth`, `pagination`, `passwords`, `validation`. For this reason, we keep the respective language files in their proper language sub-folders
- the moment a new language is added from the interface
    - the new language is saved in the database
    - the four php translation files are copied to a newly created language folder
    - a new JSON language file is generated, containing the keys for the existing translations. The keys are collected using as reference the first existing JSON file
- when deleting a language
    - the language is removed from the database
    - the language folder and its contents are removed
    - the JSON language file is removed

### Publishes

- `php artisan vendor:publish --tag=localisation-component` - the VueJS component
- `php artisan vendor:publish --tag=enso-update` - a common alias for when wanting to update the VueJS component,
once a newer version is released

### TO DO

- [ ] sync-json command / button

### Notes

The [Laravel Enso Core](https://github.com/laravel-enso/Core) package comes with this package included.

<!--h-->
### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
<!--/h-->