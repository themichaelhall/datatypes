<?php

namespace DataTypes;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Interfaces\SchemeInterface;

/**
 * Class representing a scheme.
 */
class Scheme implements SchemeInterface
{
    /**
     * Constructs a scheme.
     *
     * @param string $scheme The scheme.
     */
    public function __construct($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        if (!static::_parse($scheme, $error)) {
            throw new SchemeInvalidArgumentException($error);
        }

        $this->_value = $scheme;
    }

    /**
     * @return string The scheme as a string.
     */
    public function __toString()
    {
        return $this->_value;
    }

    /**
     * Checks if a scheme is valid.
     *
     * @param string $scheme The scheme.
     *
     * @return bool True if the $scheme parameter is a valid scheme, false otherwise.
     */
    public static function isValid($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        return static::_parse($scheme);
    }

    /**
     * Parses a scheme and returns a Scheme instance.
     *
     * @param string $scheme The scheme.
     *
     * @return SchemeInterface|null The Scheme instance if the $scheme parameter is a valid scheme, null otherwise.
     */
    public static function tryParse($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        try {
            $result = new self($scheme);

            return $result;
        } catch (SchemeInvalidArgumentException $e) {
        }

        return null;
    }

    /**
     * Tries to parse a scheme and returns the result or error text.
     *
     * @param string      $scheme The scheme.
     * @param string|null $error  The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool
     */
    private static function _parse($scheme, &$error = null)
    {
        // Pre-validate scheme.
        if (!static::_preValidate($scheme, $error)) {
            return false;
        }

        // Check scheme.
        if ($scheme !== 'http' && $scheme !== 'https') {
            $error = 'Scheme "' . $scheme . '" is invalid: Scheme must be "http" or "https"';

            return false;
        }

        return true;
    }

    /**
     * Pre-validates a scheme.
     *
     * @param string      $scheme The scheme.
     * @param string|null $error  The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function _preValidate($scheme, &$error = null)
    {
        // Empty scheme is invalid.
        if ($scheme === '') {
            $error = 'Scheme "' . $scheme . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * @var string My value.
     */
    private $_value;
}
