<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes\Interfaces\Traits;

use DataTypes\Interfaces\DataTypeInterface;

/**
 * Interface for PathTrait.
 *
 * @since 1.0.0
 */
interface PathTraitInterface extends DataTypeInterface
{
    /**
     * Returns the depth of the path.
     *
     * @since 1.0.0
     *
     * @return int The depth of the path.
     */
    public function getDepth();

    /**
     * Returns the directory parts.
     *
     * @since 1.0.0
     *
     * @return string[] The directory parts.
     */
    public function getDirectoryParts();

    /**
     * Returns the filename or null if the path is a directory.
     *
     * @since 1.0.0
     *
     * @return string|null The filename or null if the path is a directory.
     */
    public function getFilename();

    /**
     * Returns true if path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is absolute, false otherwise.
     */
    public function isAbsolute();

    /**
     * Returns true if path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is a directory, false otherwise.
     */
    public function isDirectory();

    /**
     * Returns true if path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is a file, false otherwise.
     */
    public function isFile();

    /**
     * Returns true if path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is relative, false otherwise.
     */
    public function isRelative();
}
