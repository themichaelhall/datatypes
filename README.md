# Data Types

[![Build Status](https://travis-ci.org/themichaelhall/datatypes.svg?branch=master)](https://travis-ci.org/themichaelhall/datatypes)
[![codecov.io](https://codecov.io/gh/themichaelhall/datatypes/coverage.svg?branch=master)](https://codecov.io/gh/themichaelhall/datatypes?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/e96f09113da841750bb5/maintainability)](https://codeclimate.com/github/themichaelhall/datatypes/maintainability)
[![StyleCI](https://styleci.io/repos/60113501/shield?style=flat)](https://styleci.io/repos/60113501)
[![License](https://poser.pugx.org/datatypes/datatypes/license)](https://packagist.org/packages/datatypes/datatypes)
[![Latest Stable Version](https://poser.pugx.org/datatypes/datatypes/v/stable)](https://packagist.org/packages/datatypes/datatypes)
[![Total Downloads](https://poser.pugx.org/datatypes/datatypes/downloads)](https://packagist.org/packages/datatypes/datatypes)

Data Types is a collection of data types classes for PHP.

## Requirements

- v1.x: PHP >= 5.6
- v2.x: PHP >= 7.1

## Install with Composer

``` bash
$ composer require datatypes/datatypes
```

## Basic usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

// Parse a url.
$url = \DataTypes\Url::parse('https://www.example.com/foo/bar');

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
$email = \DataTypes\EmailAddress::parse('foo.bar@example.com');

// Prints "example.com".
echo $email->getHost();

// Returns false.
\DataTypes\Url::isValid('foo');
// Returns null.
\DataTypes\Url::tryParse('foo');
// Throws \DataTypes\Exceptions\UrlInvalidArgumentException.
\DataTypes\Url::parse('foo');
```

## License

MIT