<?php

namespace DataTypes\Interfaces;

use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\FilePath;
use DataTypes\Interfaces\Base\AbstractPathInterface;

/**
 * Interface for FilePath class.
 */
interface FilePathInterface extends AbstractPathInterface
{
    /**
     * Returns the directory of the file path.
     *
     * @version 1.0.0
     *
     * @return FilePath The directory of the file path.
     */
    public function getDirectory();

    /**
     * Returns the file path as a relative path.
     *
     * @version 1.0.0
     *
     * @return FilePath The file path as a relative path.
     */
    public function toRelative();

    /**
     * Returns the file path as an absolute path.
     *
     * @version 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePath The file path as an absolute path.
     */
    public function toAbsolute();

    /**
     * Returns a copy of the file path combined with another file path.
     *
     * @version 1.0.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @throws FilePathLogicException if the file paths could not be combined.
     *
     * @return FilePath The combined file path.
     */
    public function withFilePath(FilePathInterface $filePath);
}
