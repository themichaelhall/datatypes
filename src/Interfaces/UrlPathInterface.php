<?php

namespace DataTypes\Interfaces;

/**
 * Interface for UrlPath class.
 */
interface UrlPathInterface extends DataTypeInterface
{
    /**
     * @return int The depth of the url path.
     */
    public function getDepth();

    /**
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory();

    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts();

    /**
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename();

    /**
     * @return bool True if url path is absolute, false otherwise.
     */
    public function isAbsolute();

    /**
     * @return bool True if url path is a directory, false otherwise.
     */
    public function isDirectory();

    /**
     * @return bool True if url path is a file, false otherwise.
     */
    public function isFile();

    /**
     * @return bool True if url path is relative, false otherwise.
     */
    public function isRelative();

    /**
     * @return UrlPathInterface The url path as a absolute path.
     */
    public function toAbsolute();

    /**
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative();
}
