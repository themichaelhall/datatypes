<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Net\Exceptions\HostnameInvalidArgumentException;
use InvalidArgumentException;

/**
 * Class representing a hostname.
 *
 * @since 1.0.0
 */
class Hostname implements HostnameInterface
{
    /**
     * Returns true if the hostname equals other hostname, false otherwise.
     *
     * @since 1.2.0
     *
     * @param HostnameInterface $hostname The other hostname.
     *
     * @return bool True if the hostname equals other hostname, false otherwise.
     */
    public function equals(HostnameInterface $hostname): bool
    {
        return $this->getDomainParts() === $hostname->getDomainParts() && $this->getTld() === $hostname->getTld();
    }

    /**
     * Returns the domain name including top-level domain.
     *
     * @since 1.0.0
     *
     * @return string The domain name including top-level domain.
     */
    public function getDomainName(): string
    {
        return $this->domainParts[count($this->domainParts) - 1] . ($this->tld !== null ? '.' . $this->tld : '');
    }

    /**
     * Returns the domain parts.
     *
     * @since 1.0.0
     *
     * @return string[] The domain parts.
     */
    public function getDomainParts(): array
    {
        return $this->domainParts;
    }

    /**
     * Returns the top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     *
     * @since 1.0.0
     *
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld(): ?string
    {
        return $this->tld;
    }

    /**
     * Returns a copy of the Hostname instance with the specified top-level domain.
     *
     * @since 1.0.0
     *
     * @param string $tld The top-level domain.
     *
     * @throws HostnameInvalidArgumentException If the top-level domain parameter is not a valid top-level domain.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public function withTld(string $tld): HostnameInterface
    {
        if (!self::validateTld($tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        self::normalizeTld($tld);

        return new self($this->domainParts, $tld);
    }

    /**
     * Returns the hostname as a string.
     *
     * @since 1.0.0
     *
     * @return string The hostname as a string.
     */
    public function __toString(): string
    {
        return implode('.', $this->domainParts) . ($this->tld !== null ? '.' . $this->tld : '');
    }

    /**
     * Creates a hostname from hostname parts.
     *
     * @since 1.0.0
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $tld         The top level domain or null if no top-level domain should be included.
     *
     * @throws HostnameInvalidArgumentException If any of the parameters are invalid.
     * @throws InvalidArgumentException         If the $domainParts parameter is not an array of strings.
     *
     * @return HostnameInterface The hostname instance.
     */
    public static function fromParts(array $domainParts, ?string $tld = null): HostnameInterface
    {
        if (count($domainParts) === 0) {
            throw new HostnameInvalidArgumentException('Domain parts [] is empty.');
        }

        if (!self::validateDomainParts($domainParts, $error)) {
            throw new HostnameInvalidArgumentException('Domain parts ["' . implode('", "', $domainParts) . '"] is invalid: ' . $error);
        }

        if (!self::validateTld($tld, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        self::normalizeDomainParts($domainParts);
        self::normalizeTld($tld);

        return new self($domainParts, $tld);
    }

    /**
     * Checks if a hostname is valid.
     *
     * @since 1.0.0
     *
     * @param string $hostname The hostname.
     *
     * @return bool True if the $hostname parameter is a valid hostname, false otherwise.
     */
    public static function isValid(string $hostname): bool
    {
        return self::doParse($hostname) !== null;
    }

    /**
     * Parses a hostname.
     *
     * @since 1.0.0
     *
     * @param string $hostname The hostname.
     *
     * @throws HostnameInvalidArgumentException If the $hostname parameter is not a valid hostname.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public static function parse(string $hostname): HostnameInterface
    {
        $result = self::doParse($hostname, $error);
        if ($result === null) {
            throw new HostnameInvalidArgumentException($error);
        }

        return $result;
    }

    /**
     * Parses a hostname.
     *
     * @since 1.0.0
     *
     * @param string $hostname The hostname.
     *
     * @return HostnameInterface|null The Hostname instance if the $hostname parameter is a valid hostname, null otherwise.
     */
    public static function tryParse(string $hostname): ?HostnameInterface
    {
        return self::doParse($hostname);
    }

    /**
     * Constructs a hostname from hostname parts.
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $tld         The top-level domain if top-level domain is present, null otherwise.
     */
    private function __construct(array $domainParts, ?string $tld)
    {
        $this->domainParts = $domainParts;
        $this->tld = $tld;
    }

    /**
     * Tries to parse a hostname and returns the result or error text.
     *
     * @param string      $str   The hostname to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The hostname if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        if ($str === '') {
            $error = 'Hostname "' . $str . '" is empty.';

            return null;
        }

        if (strlen($str) > 255) {
            $error = 'Hostname "' . $str . '" is too long: Maximum allowed length is 255 characters."';

            return null;
        }

        $domainParts = explode(
            '.',
            substr($str, -1) === '.' ? substr($str, 0, -1) : $str // Remove trailing "." from hostname.
        );

        $tld = null;
        if (count($domainParts) > 1) {
            $tld = array_pop($domainParts);
        }

        if (!self::validateDomainParts($domainParts, $error)) {
            $error = 'Hostname "' . $str . '" is invalid: ' . $error;

            return null;
        }

        if (!self::validateTld($tld, $error)) {
            $error = 'Hostname "' . $str . '" is invalid: ' . $error;

            return null;
        }

        self::normalizeDomainParts($domainParts);
        self::normalizeTld($tld);

        return new self($domainParts, $tld);
    }

    /**
     * Validates a top-level domain.
     *
     * @param string|null $tld   The top-level domain.
     * @param string|null $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateTld(?string $tld, ?string &$error): bool
    {
        if ($tld === null) {
            return true;
        }

        if ($tld === '') {
            $error = 'Top-level domain "' . $tld . '" is empty.';

            return false;
        }

        if (strlen($tld) > 63) {
            $error = 'Top-level domain "' . $tld . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        if (preg_match('/[^a-zA-Z]/', $tld, $matches)) {
            $error = 'Top-level domain "' . $tld . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates domain parts.
     *
     * @param string[]    $domainParts The domain parts.
     * @param string|null $error       The error text if validation was not successful, undefined otherwise.
     *
     * @throws InvalidArgumentException If the $domainParts parameter is not an array of strings.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateDomainParts(array $domainParts, ?string &$error): bool
    {
        foreach ($domainParts as $part) {
            if (!is_string($part)) {
                throw new InvalidArgumentException('$domainParts parameter is not an array of strings.');
            }

            if (!self::validateDomainPart($part, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates a domain part.
     *
     * @param string      $domainPart The domain part.
     * @param string|null $error      The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateDomainPart(string $domainPart, ?string &$error): bool
    {
        if ($domainPart === '') {
            $error = 'Part of domain "' . $domainPart . '" is empty.';

            return false;
        }

        if (strlen($domainPart) > 63) {
            $error = 'Part of domain "' . $domainPart . '" is too long: Maximum allowed length is 63 characters.';

            return false;
        }

        if (preg_match('/[^a-zA-Z0-9-]/', $domainPart, $matches)) {
            $error = 'Part of domain "' . $domainPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        if (substr($domainPart, 0, 1) === '-') {
            $error = 'Part of domain "' . $domainPart . '" begins with "-".';

            return false;
        }

        if (substr($domainPart, -1) === '-') {
            $error = 'Part of domain "' . $domainPart . '" ends with "-".';

            return false;
        }

        return true;
    }

    /**
     * Normalizes domain parts.
     *
     * @param string[] $domainParts The domain parts.
     */
    private static function normalizeDomainParts(array &$domainParts): void
    {
        array_walk($domainParts, function (&$part) {
            $part = strtolower($part);
        });
    }

    /**
     * Normalizes a top-level domain.
     *
     * @param string|null $tld The top-level domain.
     */
    private static function normalizeTld(?string &$tld = null): void
    {
        if ($tld === null) {
            return;
        }

        $tld = strtolower($tld);
    }

    /**
     * @var string[] My domain parts.
     */
    private $domainParts;

    /**
     * @var string|null My top-level domain if this hostname has a top-level domain, null otherwise.
     */
    private $tld;
}
