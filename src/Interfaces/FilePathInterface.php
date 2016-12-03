<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes\Interfaces;

use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\Interfaces\Traits\PathTraitInterface;

/**
 * Interface for FilePath class.
 *
 * @since 1.0.0
 */
interface FilePathInterface extends PathTraitInterface
{
    /**
     * Returns the directory of the file path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The directory of the file path.
     */
    public function getDirectory();

    /**
     * Returns the drive of the file path or null if no drive is present or supported.
     *
     * @since 1.0.0
     *
     * @return string|null The drive of the file path or null if no drive is present or supported.
     */
    public function getDrive();

    /**
     * Returns the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePathInterface The file path as an absolute path.
     */
    public function toAbsolute();

    /**
     * Returns the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The file path as a relative path.
     */
    public function toRelative();

    /**
     * Returns a copy of the file path combined with another file path.
     *
     * @since 1.0.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @throws FilePathLogicException if the file paths could not be combined.
     *
     * @return FilePathInterface The combined file path.
     */
    public function withFilePath(FilePathInterface $filePath);
}
