<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Interfaces\SchemeInterface;

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
    const TYPE_HTTP = 1;

    /**
     * Scheme type https.
     *
     * @since 1.0.0
     */
    const TYPE_HTTPS = 2;

    /**
     * Returns true if the scheme equals other scheme, false otherwise.
     *
     * @since 1.0.0
     *
     * @param SchemeInterface $scheme The other scheme.
     *
     * @return bool True if the scheme equals other scheme, false otherwise.
     */
    public function equals(SchemeInterface $scheme)
    {
        return $this->myType === $scheme->getType();
    }

    /**
     * Returns the default port of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The default port of the scheme.
     */
    public function getDefaultPort()
    {
        return $this->myDefaultPort;
    }

    /**
     * Returns the type of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The type of the scheme.
     */
    public function getType()
    {
        return $this->myType;
    }

    /**
     * Returns true if the scheme is http, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is http, false otherwise.
     */
    public function isHttp()
    {
        return $this->myType === self::TYPE_HTTP;
    }

    /**
     * Returns true if the scheme is https, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is https, false otherwise.
     */
    public function isHttps()
    {
        return $this->myType === self::TYPE_HTTPS;
    }

    /**
     * Returns the scheme as a string.
     *
     * @since 1.0.0
     *
     * @return string The scheme as a string.
     */
    public function __toString()
    {
        return $this->myScheme;
    }

    /**
     * Checks if a scheme is valid.
     *
     * @since 1.0.0
     *
     * @param string $scheme The scheme.
     *
     * @throws \InvalidArgumentException If the $scheme parameter is not a string.
     *
     * @return bool True if the $scheme parameter is a valid scheme, false otherwise.
     */
    public static function isValid($scheme)
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('$scheme parameter is not a string.');
        }

        return self::myParse($scheme);
    }

    /**
     * Parses a scheme.
     *
     * @since 1.0.0
     *
     * @param string $scheme The scheme.
     *
     * @throws \InvalidArgumentException      If the $scheme parameter is not a string.
     * @throws SchemeInvalidArgumentException If the $scheme parameter is not a valid scheme.
     *
     * @return SchemeInterface The Scheme instance.
     */
    public static function parse($scheme)
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('$scheme parameter is not a string.');
        }

        if (!self::myParse($scheme, $result, $type, $defaultPort, $error)) {
            throw new SchemeInvalidArgumentException($error);
        }

        return new self($result, $type, $defaultPort);
    }

    /**
     * Parses a scheme.
     *
     * @since 1.0.0
     *
     * @param string $scheme The scheme.
     *
     * @throws \InvalidArgumentException If the $scheme parameter is not a string.
     *
     * @return SchemeInterface|null The Scheme instance if the $scheme parameter is a valid scheme, null otherwise.
     */
    public static function tryParse($scheme)
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('$scheme parameter is not a string.');
        }

        if (!self::myParse($scheme, $result, $type, $defaultPort)) {
            return null;
        }

        return new self($result, $type, $defaultPort);
    }

    /**
     * Constructs a scheme from scheme info.
     *
     * @param string $scheme      The scheme.
     * @param int    $type        The type.
     * @param int    $defaultPort The default port.
     */
    private function __construct($scheme, $type, $defaultPort)
    {
        $this->myScheme = $scheme;
        $this->myType = $type;
        $this->myDefaultPort = $defaultPort;
    }

    /**
     * Tries to parse a scheme and returns the result or error text.
     *
     * @param string      $scheme      The scheme.
     * @param string|null $result      The result if parsing was successful, undefined otherwise.
     * @param int|null    $type        The type if parsing was successful, undefined otherwise.
     * @param int|null    $defaultPort The default port if parsing was successful, undefined otherwise.
     * @param string|null $error       The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($scheme, &$result = null, &$type = null, &$defaultPort = null, &$error = null)
    {
        if ($scheme === '') {
            $error = 'Scheme "' . $scheme . '" is empty.';

            return false;
        }

        $result = strtolower($scheme);

        if (!isset(self::$mySchemes[$result])) {
            $error = 'Scheme "' . $scheme . '" is invalid: Scheme must be "http" or "https".';

            return false;
        }

        $schemeInfo = self::$mySchemes[$result];
        $type = $schemeInfo[0];
        $defaultPort = $schemeInfo[1];

        return true;
    }

    /**
     * @var int My default port.
     */
    private $myDefaultPort;

    /**
     * @var string My scheme.
     */
    private $myScheme;

    /**
     * @var int My type.
     */
    private $myType;

    /**
     * @var array The valid schemes.
     */
    private static $mySchemes = [
        'http'  => [self::TYPE_HTTP, 80],
        'https' => [self::TYPE_HTTPS, 443],
    ];
}
