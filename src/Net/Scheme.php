<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Net\Exceptions\SchemeInvalidArgumentException;

/**
 * Class representing a scheme.
 *
 * @since 1.0.0
 */
class Scheme implements SchemeInterface
{
    /**
     * Scheme type http.
     *
     * @since 1.0.0
     */
    public const TYPE_HTTP = 1;

    /**
     * Scheme type https.
     *
     * @since 1.0.0
     */
    public const TYPE_HTTPS = 2;

    /**
     * Returns true if the scheme equals other scheme, false otherwise.
     *
     * @since 1.2.0
     *
     * @param SchemeInterface $scheme The other scheme.
     *
     * @return bool True if the scheme equals other scheme, false otherwise.
     */
    public function equals(SchemeInterface $scheme): bool
    {
        return $this->type === $scheme->getType();
    }

    /**
     * Returns the default port of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The default port of the scheme.
     */
    public function getDefaultPort(): int
    {
        return $this->defaultPort;
    }

    /**
     * Returns the type of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The type of the scheme.
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Returns true if the scheme is http, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is http, false otherwise.
     */
    public function isHttp(): bool
    {
        return $this->type === self::TYPE_HTTP;
    }

    /**
     * Returns true if the scheme is https, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is https, false otherwise.
     */
    public function isHttps(): bool
    {
        return $this->type === self::TYPE_HTTPS;
    }

    /**
     * Returns the scheme as a string.
     *
     * @since 1.0.0
     *
     * @return string The scheme as a string.
     */
    public function __toString(): string
    {
        return $this->scheme;
    }

    /**
     * Checks if a scheme is valid.
     *
     * @since 1.0.0
     *
     * @param string $string The scheme.
     *
     * @return bool True if the scheme parameter is a valid scheme, false otherwise.
     */
    public static function isValid(string $string): bool
    {
        return self::doParse($string) !== null;
    }

    /**
     * Parses a scheme.
     *
     * @since 1.0.0
     *
     * @param string $string The scheme.
     *
     * @throws SchemeInvalidArgumentException If the scheme parameter is not a valid scheme.
     *
     * @return SchemeInterface The Scheme instance.
     */
    public static function parse(string $string): SchemeInterface
    {
        $result = self::doParse($string, $error);
        if ($result === null) {
            throw new SchemeInvalidArgumentException($error);
        }

        return $result;
    }

    /**
     * Parses a scheme.
     *
     * @since 1.0.0
     *
     * @param string $string The scheme.
     *
     * @return SchemeInterface|null The Scheme instance if the scheme parameter is a valid scheme, null otherwise.
     */
    public static function tryParse(string $string): ?SchemeInterface
    {
        return self::doParse($string);
    }

    /**
     * Constructs a scheme from scheme info.
     *
     * @param string $scheme      The scheme.
     * @param int    $type        The type.
     * @param int    $defaultPort The default port.
     */
    private function __construct(string $scheme, int $type, int $defaultPort)
    {
        $this->scheme = $scheme;
        $this->type = $type;
        $this->defaultPort = $defaultPort;
    }

    /**
     * Tries to parse a scheme and returns the result or error text.
     *
     * @param string      $str   The scheme to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return Scheme|null The result if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        if ($str === '') {
            $error = 'Scheme "' . $str . '" is empty.';

            return null;
        }

        $schemeKey = strtolower($str);

        $schemeInfo = self::SCHEMES_INFO[$schemeKey] ?? null;
        if ($schemeInfo === null) {
            $error = 'Scheme "' . $str . '" is invalid: Scheme must be "http" or "https".';

            return null;
        }

        $type = $schemeInfo[0];
        $defaultPort = $schemeInfo[1];

        return new self($schemeKey, $type, $defaultPort);
    }

    /**
     * @var int The default port.
     */
    private int $defaultPort;

    /**
     * @var string The scheme.
     */
    private string $scheme;

    /**
     * @var int The type.
     */
    private int $type;

    /**
     * @var array The valid schemes.
     */
    private const SCHEMES_INFO = [
        'http'  => [self::TYPE_HTTP, 80],
        'https' => [self::TYPE_HTTPS, 443],
    ];
}
