# JSON Translation Helper for Laravel
JSON Translation Helper scans your project for `__()`, `lang()` translation helper methods and `@lang` directives, then it creates keys in your JSON translation files. 

And yes, it avoids duplicates – helper creates only non-existing keys. Ready for your translation.

## Installation

First, install JSON Translation Helper using the Composer require command:

```
composer require subotkevic/laravel-json-translation-helper
```

That's it. Package registers service provider automatically.

## Usage

### Translation files

First, you have to create your translation files for languages you will translate your application to. 

For example, if you want your application to have a Spanish translation, you should create a `resources/lang/es.json` file.

Of course you can have multiple translation files:
```
resources/
    lang/
        es.json
        fr.json
```

Make sure that your translation files is valid JSON, otherwise our package will not work:

```
{
    "I love programming.": "Me encanta programar."
}
```

If you don't have any translations for now, just **make sure your file is not empty**, but actually an empty JSON object:
```
{}
```

### Scan your application

Finally, to scan your application for missing translation keys just run:

```
php artisan translation:scan
```

## Customization

You can change the default paths to scan your application from, the output directory where your JSON translation files are located, and even the file extensions you want to scan from.

First, publish the configuration file:

```
php artisan vendor:publish --provider="JsonTranslationHelper\TranslationHelperServiceProvider"
```

Then in the `config/translation-helper.php` you can change default values of `scan_directories`, `file_extensions` and `output_directory`.