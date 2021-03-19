<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Interfaces;

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\Exceptions\FilePathLogicException;

/**
 * Interface for FilePath class.
 *
 * @since 1.0.0
 */
interface FilePathInterface extends PathInterface
{
    /**
     * Returns true if the file path equals other file path, false otherwise.
     *
     * @since 1.2.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @return bool True if the file path equals other file path, false otherwise.
     */
    public function equals(self $filePath): bool;

    /**
     * Returns the directory of the file path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The directory of the file path.
     */
    public function getDirectory(): self;

    /**
     * Returns the drive of the file path or null if no drive is present or supported.
     *
     * @since 1.0.0
     *
     * @return string|null The drive of the file path or null if no drive is present or supported.
     */
    public function getDrive(): ?string;

    /**
     * Returns the parent directory of the file path or null if file path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface|null The parent directory of the file path or null if file path does not have a parent directory.
     */
    public function getParentDirectory(): ?self;

    /**
     * Returns a copy of the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePathInterface The file path as an absolute path.
     */
    public function toAbsolute(): self;

    /**
     * Returns a copy of the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The file path as a relative path.
     */
    public function toRelative(): self;

    /**
     * Returns a copy of the file path with another filename.
     *
     * @since 2.2.0
     *
     * @param string $filename The other filename
     *
     * @throws FilePathInvalidArgumentException if the filename if invalid.
     *
     * @return FilePathInterface The new file path.
     */
    public function withFilename(string $filename): self;

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
    public function withFilePath(self $filePath): self;
}
