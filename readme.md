# SettingsModule

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Settings list.

## Installation

Via Composer

``` bash
$ composer require zdrojowa/settings-module
```

## NPM required:

``` bash
"axios": "^0.19",
"bootstrap": "^4.5.3",
"bootstrap-vue": "2.16.0",
"vue": "^2.6.10",
"vue-router": "^3.4.9",
```

## Usage
- Add in webpack.mix.js

``` bash
mix.module('SettingsModule', 'vendor/zdrojowa/settings-module');
```

- Add module SettingsModule in config/selene.php

``` bash
'modules' => [
    SettingsModule::class,
],

'menu-order' => [
    'SettingsModule',
],
```

- run npm

``` bash
npm install
npm run prod
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/zdrojowa/settings-module.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zdrojowa/settings-module.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/zdrojowa/settings-module/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zdrojowa/settings-module
[link-downloads]: https://packagist.org/packages/zdrojowa/settings-module
[link-travis]: https://travis-ci.org/zdrojowa/settings-module
[link-author]: https://github.com/zdrojowa
[link-contributors]: ../../contributors
