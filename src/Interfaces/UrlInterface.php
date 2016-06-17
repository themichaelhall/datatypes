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
}
