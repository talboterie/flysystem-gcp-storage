# Flysystem GCP Storage

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![GitHub Workflow Status][ico-github]][link-github]
[![Coverage Status][ico-codecov]][link-codecov]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Flysystem adapter for Google Cloud Storage.

## Install

Via Composer

``` bash
$ composer require talboterie/flysystem-gcp-storage
```

## Usage

``` php
use League\Flysystem\Filesystem;
use Google\Cloud\Storage\StorageClient;
use Talboterie\FlysystemGCPStorage\StorageAdapter;

$client = new StorageClient([
    'projectId' => 'your-gcp-project-id',
    'keyFilePath' => '/path/to/credentials.json',
]);

$adapter = new StorageAdapter($client->bucket('your-bucket'));

$filesystem = new Filesystem($adapter);
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

[ico-version]: https://img.shields.io/packagist/v/talboterie/flysystem-gcp-storage.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-github]: https://img.shields.io/github/workflow/status/talboterie/flysystem-gcp-storage/run-tests?style=flat-square
[ico-codecov]: https://img.shields.io/codecov/c/gh/talboterie/flysystem-gcp-storage?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/talboterie/flysystem-gcp-storage.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/talboterie/flysystem-gcp-storage.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/talboterie/flysystem-gcp-storage
[link-github]: https://github.com/talboterie/flysystem-gcp-storage/actions
[link-codecov]: https://codecov.io/gh/talboterie/flysystem-gcp-storage
[link-code-quality]: https://scrutinizer-ci.com/g/talboterie/flysystem-gcp-storage
[link-downloads]: https://packagist.org/packages/talboterie/flysystem-gcp-storage
[link-author]: https://github.com/vtalbot
[link-contributors]: ../../contributors
