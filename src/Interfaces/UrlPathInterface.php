<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
namespace DataTypes\Interfaces;

/**
 * Interface for UrlPath class.
 *
 * @since 1.0.0
 */
interface UrlPathInterface extends DataTypeInterface
{
    /**
     * Returns the depth of the url path.
     *
     * @since 1.0.0
     *
     * @return int The depth of the url path.
     */
    public function getDepth();

    /**
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory();

    /**
     * Returns the directory parts of the url path.
     *
     * @since 1.0.0
     *
     * @return string[] The directory parts.
     */
    public function getDirectoryParts();

    /**
     * Returns the filename or null if the url path is a directory.
     *
     * @since 1.0.0
     *
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename();

    /**
     * Returns true if url path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is absolute, false otherwise.
     */
    public function isAbsolute();

    /**
     * Returns true if url path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a directory, false otherwise.
     */
    public function isDirectory();

    /**
     * Returns true if url path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a file, false otherwise.
     */
    public function isFile();

    /**
     * Return true if url path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is relative, false otherwise.
     */
    public function isRelative();

    /**
     * Returns the url path as a absolute path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a absolute path.
     */
    public function toAbsolute();

    /**
     * Returns the url path as a relative path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative();

    /**
     * Returns a copy of the url path combined with another url path.
     *
     * @since 1.0.0
     *
     * @param UrlPathInterface $urlPath The other url path.
     *
     * @return UrlPathInterface The combined url path.
     */
    public function withUrlPath(UrlPathInterface $urlPath);
}
