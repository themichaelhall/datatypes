<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
namespace DataTypes\Interfaces;

use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\FilePath;
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
     * @return FilePath The directory of the file path.
     */
    public function getDirectory();

    /**
     * Returns the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePath The file path as a relative path.
     */
    public function toRelative();

    /**
     * Returns the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePath The file path as an absolute path.
     */
    public function toAbsolute();

    /**
     * Returns a copy of the file path combined with another file path.
     *
     * @since 1.0.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @throws FilePathLogicException if the file paths could not be combined.
     *
     * @return FilePath The combined file path.
     */
    public function withFilePath(FilePathInterface $filePath);
}
