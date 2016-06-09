<?php

namespace DataTypes;

use DataTypes\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Interfaces\HostnameInterface;

/**
 * Class representing a hostname.
 */
class Hostname implements HostnameInterface
{
    /**
     * Constructs a hostname.
     *
     * @param string $hostname The hostname..
     *
     * @throws HostnameInvalidArgumentException If the $hostname parameter is not a valid hostname.
     */
    public function __construct($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        if (!static::_parse($hostname, false, $domainParts, $tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        $this->_build($domainParts, $tld);
    }

    /**
     * @return string The domain name including top-level domain.
     */
    public function getDomain()
    {
        return $this->_domainParts[count($this->_domainParts) - 1] . ($this->_tld !== null ? '.' . $this->_tld : '');
    }

    /**
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld()
    {
        return $this->_tld;
    }

    /**
     * @return string The hostname as a string.
     */
    public function __toString()
    {
        return implode('.', $this->_domainParts) . ($this->_tld !== null ? '.' . $this->_tld : '');
    }

    /**
     * Checks if a hostname is valid.
     *
     * @param string $hostname The hostname.
     *
     * @return bool True if the $hostname parameter is a valid hostname, false otherwise.
     */
    public static function isValid($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        return static::_parse($hostname, true);
    }

    /**
     * Parses a hostname and returns a Hostname instance.
     *
     * @param string $hostname The hostname.
     *
     * @return Hostname|null The Hostname instance if the $hostname parameter is a valid hostname, null otherwise.
     */
    public static function tryParse($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        try {
            $result = new self($hostname);

            return $result;
        } catch (HostnameInvalidArgumentException $e) {
        }

        return null;
    }

    /**
     * Builds this hostname from hostname parts.
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $tld         The top-level domain if top-level domain is present, null otherwise.
     */
    private function _build(array $domainParts, $tld = null)
    {
        $this->_domainParts = $domainParts;
        $this->_tld = $tld;
    }

    /**
     * Tries to parse a hostname and returns the result or error text.
     *
     * @param string        $hostname     The hostname.
     * @param bool          $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string[]|null $domainParts  The domain parts if parsing was successful, undefined otherwise.
     * @param string|null   $tld          The top-level domain if parsing was successful, undefined otherwise.
     * @param string|null   $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($hostname, $validateOnly, array &$domainParts = null, &$tld = null, &$error = null)
    {
        // Pre-validate hostname.
        if (!static::_preValidate($hostname, $error)) {
            return false;
        }

        // Split hostname in parts.
        $domainParts = explode(
            '.',
            substr($hostname, -1) === '.' ? substr($hostname, 0, -1) : $hostname // Remove trailing "." from hostname.
        );

        // Is there a top-level domain?
        $tld = count($domainParts) > 1 ? array_pop($domainParts) : null;

        if ($tld !== null) {
            if (!static::_validateTld($tld, $error)) {
                $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

                return false;
            }
        }

        // Validate the domain parts.
        foreach ($domainParts as $part) {
            if (!static::_validateDomainPart($part, $error)) {
                $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

                return false;
            }
        }

        if (!$validateOnly) {
            // Normalize result.
            $tld = $tld !== null ? strtolower($tld) : null;
            array_walk($domainParts, function (&$p) {
                $p = strtolower($p);
            });
        }

        return true;
    }

    /**
     * Pre-validates a hostname.
     *
     * @param string $hostname The hostname.
     * @param string $error    The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function _preValidate($hostname, &$error)
    {
        // Empty hostname is invalid.
        if ($hostname === '') {
            $error = 'Hostname "' . $hostname . '" is empty.';

            return false;
        }

        // Hostname longer than maximum length is invalid.
        if (strlen($hostname) > 255) {
            $error = 'Hostname "' . $hostname . '" is too long: Maximum allowed length is 255 characters."';

            return false;
        }

        return true;
    }

    /**
     * Validates a top-level domain.
     *
     * @param string $tld   The top-level domain.
     * @param string $error The The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function _validateTld($tld, &$error)
    {
        // Empty top-level domain is invalid.
        if ($tld === '') {
            $error = 'Top-level domain "' . $tld . '" is empty.';

            return false;
        }

        // Too long top level domain is invalid.
        if (strlen($tld) > 63) {
            $error = 'Top-level domain "' . $tld . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        // Top-level domain containing invalid character is invalid.
        if (preg_match('/[^a-zA-Z]/', $tld, $matches)) {
            $error = 'Top-level domain "' . $tld . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates a domain part.
     *
     * @param string $domainPart The domain part.
     * @param string $error      The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function _validateDomainPart($domainPart, &$error)
    {
        // Empty hostname part is invalid.
        if ($domainPart === '') {
            $error = 'Part of hostname "' . $domainPart . '" is empty.';

            return false;
        }

        // Too long hostname part is invalid.
        if (strlen($domainPart) > 63) {
            $error = 'Part of hostname "' . $domainPart . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        // Hostname part containing invalid character is invalid.
        if (preg_match('/[^a-zA-Z0-9-]/', $domainPart, $matches)) {
            $error = 'Part of hostname "' . $domainPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        // Hostname part can not begin with a dash.
        if (substr($domainPart, 0, 1) === '-') {
            $error = 'Part of hostname "' . $domainPart . '" begins with "-".';

            return false;
        }

        // Hostname part can not end with a dash.
        if (substr($domainPart, -1) === '-') {
            $error = 'Part of hostname "' . $domainPart . '" ends with "-".';

            return false;
        }

        return true;
    }

    /**
     * @var string[] My domain parts.
     */
    private $_domainParts;

    /**
     * @var string|null My top-level domain if this hostname has a top-level domain, null otherwise.
     */
    private $_tld;
}
