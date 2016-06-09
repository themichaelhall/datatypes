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

        if (!static::_parse($hostname, false, $subdomains, $domain, $tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        $this->_build($subdomains, $domain, $tld);
    }

    /**
     * @return string The domain name including top-level domain.
     */
    public function getDomain()
    {
        if ($this->_tld === null) {
            return $this->_domain;
        }

        return $this->_domain . '.' . $this->_tld;
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
        if (count($this->_subdomains) > 0) {
            return implode('.', $this->_subdomains) . '.' . $this->getDomain();
        }

        return $this->getDomain();
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
     * @param string[]    $subdomains The subdomains.
     * @param string      $domain     The domain without top-level domain.
     * @param string|null $tld        The top-level domain if top-level domain is present, null otherwise.
     */
    private function _build(array $subdomains, $domain, $tld = null)
    {
        $this->_subdomains = $subdomains;
        $this->_domain = $domain;
        $this->_tld = $tld;
    }

    /**
     * Tries to parse a hostname and returns the result or error text.
     *
     * @param string        $hostname     The hostname.
     * @param bool          $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string[]|null $subdomains   The subdomains if parsing was successful, undefined otherwise.
     * @param string|null   $domain       The domain without top-level domain if parsing was successful, undefined otherwise.
     * @param string|null   $tld          The top-level domain if parsing was successful, undefined otherwise.
     * @param string|null   $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($hostname, $validateOnly, array &$subdomains = null, &$domain = null, &$tld = null, &$error = null)
    {
        // Pre-validate hostname.
        if (!static::_preValidate($hostname, $error)) {
            return false;
        }

        // Split hostname in parts.
        $parts = explode(
            '.',
            substr($hostname, -1) === '.' ? substr($hostname, 0, -1) : $hostname // Remove trailing "." from hostname.
        );

        // Is there a top-level domain?
        $tld = count($parts) > 1 ? array_pop($parts) : null;

        if ($tld !== null) {
            if (!static::_validateTld($tld, $error)) {
                $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

                return false;
            }
        }

        // Validate the rest.
        foreach ($parts as $part) {
            if (!static::_validatePart($part, $error)) {
                $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

                return false;
            }
        }

        if (!$validateOnly) {
            $partsCount = count($parts);

            // Copy the parts into the result.
            if ($partsCount == 1) {
                $subdomains = [];
                $domain = $parts[0];
            } else {
                $subdomains = array_slice($parts, 0, $partsCount - 1);
                $domain = $parts[$partsCount - 1];
            }

            // Normalize result.
            $tld = $tld !== null ? strtolower($tld) : null;
            $domain = strtolower($domain);
            array_walk($subdomains, function (&$p) {
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
     * Validates a hostname part.
     *
     * @param string $part  The hostname part.
     * @param string $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function _validatePart($part, &$error)
    {
        // Empty hostname part is invalid.
        if ($part === '') {
            $error = 'Part of hostname "' . $part . '" is empty.';

            return false;
        }

        // Too long hostname part is invalid.
        if (strlen($part) > 63) {
            $error = 'Part of hostname "' . $part . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        // Hostname part containing invalid character is invalid.
        if (preg_match('/[^a-zA-Z0-9-]/', $part, $matches)) {
            $error = 'Part of hostname "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        // Hostname part can not begin with a dash.
        if (substr($part, 0, 1) === '-') {
            $error = 'Part of hostname "' . $part . '" begins with "-".';

            return false;
        }

        // Hostname part can not end with a dash.
        if (substr($part, -1) === '-') {
            $error = 'Part of hostname "' . $part . '" ends with "-".';

            return false;
        }

        return true;
    }

    /**
     * @var string[] My subdomains.
     */
    private $_subdomains;

    /**
     * @var string My domain name, without top-level domain.
     */
    private $_domain;

    /**
     * @var string|null My top-level domain if this hostname has a top-level domain, null otherwise.
     */
    private $_tld;
}
