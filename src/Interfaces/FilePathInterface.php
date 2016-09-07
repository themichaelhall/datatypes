<?php

namespace DataTypes\Interfaces;

/**
 * Interface for FilePath class.
 */
interface FilePathInterface extends DataTypeInterface
{
    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts();

    /**
     * @return string|null The filename or null if the file path is a directory.
     */
    public function getFilename();

    /**
     * @return bool True if file path is absolute, false otherwise.
     */
    public function isAbsolute();

    /**
     * @return bool True if file path is relative, false otherwise.
     */
    public function isRelative();
}
