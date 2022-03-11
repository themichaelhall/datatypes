<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Common\ParsableDataTypeInterface;
use DataTypes\Net\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Net\Exceptions\UrlPathLogicException;

/**
 * Interface for UrlPath class.
 *
 * @since 1.0.0
 */
interface UrlPathInterface extends ParsableDataTypeInterface
{
    /**
     * Returns true if the url path equals other url path, false otherwise.
     *
     * @since 1.2.0
     *
     * @param UrlPathInterface $urlPath The other url path.
     *
     * @return bool True if the url path equals other url path, false otherwise.
     */
    public function equals(self $urlPath): bool;

    /**
     * Returns the depth of the url path.
     *
     * @since 1.0.0
     *
     * @return int The depth of the url path.
     */
    public function getDepth(): int;

    /**
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory(): self;

    /**
     * Returns the directory parts.
     *
     * @since 1.0.0
     *
     * @return string[] The directory parts.
     */
    public function getDirectoryParts(): array;

    /**
     * Returns the filename or null if the path is a directory.
     *
     * @since 1.0.0
     *
     * @return string|null The filename or null if the path is a directory.
     */
    public function getFilename(): ?string;

    /**
     * Returns the parent directory of the url path or null if url path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface|null The parent directory of the url path or null if url path does not have a parent directory.
     */
    public function getParentDirectory(): ?self;

    /**
     * Returns true if url path has a parent directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path has a parent directory, false otherwise.
     */
    public function hasParentDirectory(): bool;

    /**
     * Returns true if url path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is absolute, false otherwise.
     */
    public function isAbsolute(): bool;

    /**
     * Returns true if url path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a directory, false otherwise.
     */
    public function isDirectory(): bool;

    /**
     * Returns true if url path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a file, false otherwise.
     */
    public function isFile(): bool;

    /**
     * Returns true if url path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is relative, false otherwise.
     */
    public function isRelative(): bool;

    /**
     * Returns a copy of the url path as an absolute path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as an absolute path.
     */
    public function toAbsolute(): self;

    /**
     * Returns a copy of the url path as a relative path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative(): self;

    /**
     * Returns a copy of the url path with another filename.
     *
     * @since 2.2.0
     *
     * @param string $filename The other filename
     *
     * @throws UrlPathInvalidArgumentException if the filename is invalid.
     *
     * @return UrlPathInterface The new url path.
     */
    public function withFilename(string $filename): self;

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
    public function withUrlPath(self $urlPath): self;
}
