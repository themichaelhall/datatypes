<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
declare(strict_types=1);

namespace DataTypes\Interfaces;

/**
 * Interface for Scheme class.
 *
 * @since 1.0.0
 */
interface SchemeInterface extends DataTypeInterface
{
    /**
     * Returns true if the scheme equals other scheme, false otherwise.
     *
     * @since 1.2.0
     *
     * @param SchemeInterface $scheme The other scheme.
     *
     * @return bool True if the scheme equals other scheme, false otherwise.
     */
    public function equals(self $scheme): bool;

    /**
     * Returns the default port of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The default port of the scheme.
     */
    public function getDefaultPort(): int;

    /**
     * Returns the type of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The type of the scheme.
     */
    public function getType(): int;

    /**
     * Returns true if the scheme is http, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is http, false otherwise.
     */
    public function isHttp(): bool;

    /**
     * Returns true if the scheme is https, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is https, false otherwise.
     */
    public function isHttps(): bool;
}
