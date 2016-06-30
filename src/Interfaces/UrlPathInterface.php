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

    /**
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename();
}
