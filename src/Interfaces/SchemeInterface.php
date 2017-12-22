<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

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
    public function equals(self $scheme);

    /**
     * Returns the default port of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The default port of the scheme.
     */
    public function getDefaultPort();

    /**
     * Returns the type of the scheme.
     *
     * @since 1.0.0
     *
     * @return int The type of the scheme.
     */
    public function getType();

    /**
     * Returns true if the scheme is http, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is http, false otherwise.
     */
    public function isHttp();

    /**
     * Returns true if the scheme is https, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if the scheme is https, false otherwise.
     */
    public function isHttps();
}
