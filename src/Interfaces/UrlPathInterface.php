<?php

namespace DataTypes\Interfaces;

/**
 * Interface for UrlPath class.
 */
interface UrlPathInterface extends DataTypeInterface
{
    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts();
}
