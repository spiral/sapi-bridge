# Spiral Framework SAPI bridge

[![PHP Version Require](https://poser.pugx.org/spiral/sapi-bridge/require/php)](https://packagist.org/packages/spiral/sapi-bridge)
[![Latest Stable Version](https://poser.pugx.org/spiral/sapi-bridge/v/stable)](https://packagist.org/packages/spiral/sapi-bridge)
[![phpunit](https://github.com/spiral/sapi-bridge/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral/sapi-bridge/actions)
[![psalm](https://github.com/spiral/sapi-bridge/actions/workflows/psalm.yml/badge.svg)](https://github.com/spiral/sapi-bridge/actions)
[![Codecov](https://codecov.io/gh/spiral/sapi-bridge/branch/master/graph/badge.svg)](https://codecov.io/gh/spiral/sapi-bridge)
[![Total Downloads](https://poser.pugx.org/spiral/sapi-bridge/downloads)](https://packagist.org/packages/spiral/sapi-bridge)
[![type-coverage](https://shepherd.dev/github/spiral/sapi-bridge/coverage.svg)](https://shepherd.dev/github/spiral/sapi-bridge)
[![psalm-level](https://shepherd.dev/github/spiral/sapi-bridge/level.svg)](https://shepherd.dev/github/spiral/sapi-bridge)

[Framework Bundle](https://github.com/spiral/framework)

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

> **Note**
> If you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained by [Spiral Scout](https://spiralscout.com).
