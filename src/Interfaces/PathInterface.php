<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Interfaces;

/**
 * Interface for paths.
 *
 * @since 2.2.0
 */
interface PathInterface extends DataTypeInterface
{
    /**
     * Returns the depth of the path.
     *
     * @since 2.2.0
     *
     * @return int The depth of the path.
     */
    public function getDepth(): int;

    /**
     * Returns the directory parts.
     *
     * @since 2.2.0
     *
     * @return string[] The directory parts.
     */
    public function getDirectoryParts(): array;

    /**
     * Returns the filename or null if the path is a directory.
     *
     * @since 2.2.0
     *
     * @return string|null The filename or null if the path is a directory.
     */
    public function getFilename(): ?string;

    /**
     * Returns true if path has a parent directory, false otherwise.
     *
     * @since 2.2.0
     *
     * @return bool True if path has a parent directory, false otherwise.
     */
    public function hasParentDirectory(): bool;

    /**
     * Returns true if path is absolute, false otherwise.
     *
     * @since 2.2.0
     *
     * @return bool True if path is absolute, false otherwise.
     */
    public function isAbsolute(): bool;

    /**
     * Returns true if path is a directory, false otherwise.
     *
     * @since 2.2.0
     *
     * @return bool True if path is a directory, false otherwise.
     */
    public function isDirectory(): bool;

    /**
     * Returns true if path is a file, false otherwise.
     *
     * @since 2.2.0
     *
     * @return bool True if path is a file, false otherwise.
     */
    public function isFile(): bool;

    /**
     * Returns true if path is relative, false otherwise.
     *
     * @since 2.2.0
     *
     * @return bool True if path is relative, false otherwise.
     */
    public function isRelative(): bool;
}
