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
     * @return string The domain name including top-level domain.
     */
    public function getDomainName()
    {
        return $this->myDomainParts[count($this->myDomainParts) - 1] . ($this->myTld !== null ? '.' . $this->myTld : '');
    }

    /**
     * @return string[] The domain parts.
     */
    public function getDomainParts()
    {
        return $this->myDomainParts;
    }

    /**
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld()
    {
        return $this->myTld;
    }

    /**
     * Returns a copy of the Hostname instance with the specified top-level domain.
     *
     * @param string $tld The top-level domain.
     *
     * @throws HostnameInvalidArgumentException If the $tld parameter is not a valid top-level domain.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public function withTld($tld)
    {
        assert(is_string($tld), '$tld is not a string');

        if (!static::myValidateTld($tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        // Normalize top-level domain.
        static::myNormalizeTld($tld);

        return new self($this->myDomainParts, $tld);
    }

    /**
     * @return string The hostname as a string.
     */
    public function __toString()
    {
        return implode('.', $this->myDomainParts) . ($this->myTld !== null ? '.' . $this->myTld : '');
    }

    /**
     * Creates a hostname from hostname parts.
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $tld         The top level domain or null if no top-level domain should be included.
     *
     * @throws HostnameInvalidArgumentException If any of the parameters are invalid.
     *
     * @return HostnameInterface The hostname instance.
     */
    public static function fromParts(array $domainParts, $tld = null)
    {
        assert(is_string($tld) || is_null($tld), '$tld is not a string or null');

        // Empty domain parts is invalid.
        if (count($domainParts) === 0) {
            throw new HostnameInvalidArgumentException('Domain parts [] is empty.');
        }

        // Validate the domain parts.
        if (!static::myValidateDomainParts($domainParts, $error)) {
            throw new HostnameInvalidArgumentException('Domain parts ["' . implode('", "', $domainParts) . '"] is invalid: ' . $error);
        }

        // Validate top-level domain.
        if (!static::myValidateTld($tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        // Normalize parts.
        static::myNormalizeDomainParts($domainParts);
        static::myNormalizeTld($tld);

        return new self($domainParts, $tld);
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

        return static::myParse($hostname, true);
    }

    /**
     * Parses a hostname.
     *
     * @param string $hostname The hostname.
     *
     * @throws HostnameInvalidArgumentException If the $hostname parameter is not a valid hostname.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public static function parse($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        if (!static::myParse($hostname, false, $domainParts, $tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        return new self($domainParts, $tld);
    }

    /**
     * Parses a hostname.
     *
     * @param string $hostname The hostname.
     *
     * @return HostnameInterface|null The Hostname instance if the $hostname parameter is a valid hostname, null otherwise.
     */
    public static function tryParse($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        if (!static::myParse($hostname, false, $domainParts, $tld)) {
            return null;
        }

        return new self($domainParts, $tld);
    }

    /**
     * Constructs a hostname from hostname parts.
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $tld         The top-level domain if top-level domain is present, null otherwise.
     */
    private function __construct(array $domainParts, $tld = null)
    {
        $this->myDomainParts = $domainParts;
        $this->myTld = $tld;
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
    private static function myParse($hostname, $validateOnly, array &$domainParts = null, &$tld = null, &$error = null)
    {
        // Pre-validate hostname.
        if (!static::myPreValidate($hostname, $error)) {
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
            if (!static::myValidateTld($tld, $error)) {
                $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

                return false;
            }
        }

        // Validate the domain parts.
        if (!static::myValidateDomainParts($domainParts, $error)) {
            $error = 'Hostname "' . $hostname . '" is invalid: ' . $error;

            return false;
        }

        if (!$validateOnly) {
            // Normalize result.
            static::myNormalizeDomainParts($domainParts);
            static::myNormalizeTld($tld);
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
    private static function myPreValidate($hostname, &$error)
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
    private static function myValidateTld($tld, &$error)
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
     * Validates domain parts.
     *
     * @param string[] $domainParts The domain parts.
     * @param string   $error       The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateDomainParts(array $domainParts, &$error)
    {
        foreach ($domainParts as $part) {
            if (!static::myValidateDomainPart($part, $error)) {
                return false;
            }
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
    private static function myValidateDomainPart($domainPart, &$error)
    {
        assert(is_string($domainPart), '$domainPart is not a string');

        // Empty domain part is invalid.
        if ($domainPart === '') {
            $error = 'Part of domain "' . $domainPart . '" is empty.';

            return false;
        }

        // Too long domain part is invalid.
        if (strlen($domainPart) > 63) {
            $error = 'Part of domain "' . $domainPart . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        // Domain part containing invalid character is invalid.
        if (preg_match('/[^a-zA-Z0-9-]/', $domainPart, $matches)) {
            $error = 'Part of domain "' . $domainPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        // Domain part can not begin with a dash.
        if (substr($domainPart, 0, 1) === '-') {
            $error = 'Part of domain "' . $domainPart . '" begins with "-".';

            return false;
        }

        // Domain part can not end with a dash.
        if (substr($domainPart, -1) === '-') {
            $error = 'Part of domain "' . $domainPart . '" ends with "-".';

            return false;
        }

        return true;
    }

    /**
     * Normalizes a top-level domain.
     *
     * @param string|null $tld The top-level domain.
     */
    private static function myNormalizeTld(&$tld = null)
    {
        if ($tld !== null) {
            $tld = strtolower($tld);
        }
    }

    /**
     * Normalizes domain parts.
     *
     * @param string[] $domainParts The domain parts.
     */
    private static function myNormalizeDomainParts(array &$domainParts)
    {
        array_walk($domainParts, function (&$part) {
            $part = strtolower($part);
        });
    }

    /**
     * @var string[] My domain parts.
     */
    private $myDomainParts;

    /**
     * @var string|null My top-level domain if this hostname has a top-level domain, null otherwise.
     */
    private $myTld;
}
