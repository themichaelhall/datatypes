<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes\Interfaces;

use DataTypes\Exceptions\UrlPathLogicException;
use DataTypes\Interfaces\Traits\PathTraitInterface;

/**
 * Interface for UrlPath class.
 *
 * @since 1.0.0
 */
interface UrlPathInterface extends PathTraitInterface
{
    /**
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory();

    /**
     * Returns the parent directory of the url path or null if url path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface|null The parent directory of the url path or null if url path does not have a parent directory.
     */
    public function getParentDirectory();

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
     * @throws UrlPathLogicException if the url paths could not be combined.
     *
     * @return UrlPathInterface The combined url path.
     */
    public function withUrlPath(self $urlPath);
}
