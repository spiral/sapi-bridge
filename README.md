# Spiral Framework SAPI bridge

[![PHP](https://img.shields.io/packagist/php-v/spiral/sapi-bridge.svg?style=flat-square)](https://packagist.org/packages/spiral/sapi-bridge)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral/sapi-bridge.svg?style=flat-square)](https://packagist.org/packages/spiral/sapi-bridge)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spiral/sapi-bridge/run-tests?label=tests&style=flat-square)](https://github.com/spiral/sapi-bridge/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral/sapi-bridge.svg?style=flat-square)](https://packagist.org/packages/spiral/sapi-bridge)

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.0+

## Installation

You can install the package via composer:

```bash
composer require spiral/sapi-bridge
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \Spiral\Sapi\Bootloader\SapiBootloader::class,
];
```

> Note: if you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
