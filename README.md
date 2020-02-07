# Flysystem GCP Storage

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![GitHub Workflow Status][ico-github]][link-github]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
build/
docs/
config/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require talboterie/fly-sy-stem-gc-pstorage
```

## Usage

``` php
$skeleton = new Talboterie\FlysystemGCPStorage\Skeleton();
echo $skeleton->echoPhrase('Hello, Talboterie!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](.github/CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email `vincent@talbot.ninja` instead of using the issue tracker.

## Credits

- [Vincent Talbot][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/talboterie/fly-sy-stem-gc-pstorage.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github]: https://img.shields.io/github/workflow/status/talboterie/flysystem-gcp-storage/run-tests?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/talboterie/fly-sy-stem-gc-pstorage.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/talboterie/fly-sy-stem-gc-pstorage.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/talboterie/fly-sy-stem-gc-pstorage.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/talboterie/fly-sy-stem-gc-pstorage
[link-github]: https://github.com/talboterie/fly-sy-stem-gc-pstorage/actions
[link-scrutinizer]: https://scrutinizer-ci.com/g/talboterie/fly-sy-stem-gc-pstorage/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/talboterie/fly-sy-stem-gc-pstorage
[link-downloads]: https://packagist.org/packages/talboterie/fly-sy-stem-gc-pstorage
[link-author]: https://github.com/vtalbot
[link-contributors]: ../../contributors
