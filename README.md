# Data Types

[![Tests](https://github.com/themichaelhall/datatypes/workflows/tests/badge.svg?branch=master)](https://github.com/themichaelhall/datatypes/actions)
[![codecov.io](https://codecov.io/gh/themichaelhall/datatypes/coverage.svg?branch=master)](https://codecov.io/gh/themichaelhall/datatypes?branch=master)
[![StyleCI](https://styleci.io/repos/60113501/shield?style=flat&branch=master)](https://styleci.io/repos/60113501)
[![License](https://poser.pugx.org/datatypes/datatypes/license)](https://packagist.org/packages/datatypes/datatypes)
[![Latest Stable Version](https://poser.pugx.org/datatypes/datatypes/v/stable)](https://packagist.org/packages/datatypes/datatypes)
[![Total Downloads](https://poser.pugx.org/datatypes/datatypes/downloads)](https://packagist.org/packages/datatypes/datatypes)

Data Types is a collection of data types classes for PHP.

## Requirements

- PHP >= 7.3

## Install with Composer

``` bash
$ composer require datatypes/datatypes
```

## Basic usage

```php
<?php

use DataTypes\Net\EmailAddress;
use DataTypes\Net\Url;

require __DIR__ . '/vendor/autoload.php';

// Parse a url.
$url = Url::parse('https://www.example.com/foo/bar');

// Prints "https".
echo $url->getScheme();
// Prints "https://www.example.com/foo/bar?query".
echo $url->withQueryString('query');

$path = $url->getPath();

// Prints "/foo/bar".
echo $path;
// Prints "/foo/".
echo $path->getDirectory();

// Parse an email address.
$email = EmailAddress::parse('foo.bar@example.com');

// Prints "example.com".
echo $email->getHost();

// Returns false.
Url::isValid('foo');
// Returns null.
Url::tryParse('foo');
// Throws \DataTypes\Net\Exceptions\UrlInvalidArgumentException.
Url::parse('foo');
```

## License

MIT
