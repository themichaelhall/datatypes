<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Url class.
 */
interface UrlInterface extends DataTypeInterface
{
    /**
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme();

    /**
     * Returns a copy of the Url instance with the specified scheme.
     *
     * @param SchemeInterface $scheme The scheme.
     *
     * @return UrlInterface The Url instance.
     */
    public function withScheme(SchemeInterface $scheme);
}
