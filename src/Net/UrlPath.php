<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Net\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Net\Exceptions\UrlPathLogicException;

/**
 * Class representing a url path.
 *
 * @since 1.0.0
 */
class UrlPath implements UrlPathInterface
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
    public function equals(UrlPathInterface $urlPath): bool
    {
        return $this->isAbsolute() === $urlPath->isAbsolute() && $this->getDirectoryParts() === $urlPath->getDirectoryParts() && $this->getFilename() === $urlPath->getFilename();
    }

    /**
     * Returns the depth of the url path.
     *
     * @since 1.0.0
     *
     * @return int The depth of the url path.
     */
    public function getDepth(): int
    {
        return count($this->directoryParts) - $this->aboveBaseLevelCount;
    }

    /**
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory(): UrlPathInterface
    {
        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->directoryParts, null);
    }

    /**
     * Returns the directory parts.
     *
     * @since 1.0.0
     *
     * @return string[] The directory parts.
     */
    public function getDirectoryParts(): array
    {
        return $this->aboveBaseLevelCount === 0 ? $this->directoryParts : array_merge(array_fill(0, $this->aboveBaseLevelCount, '..'), $this->directoryParts);
    }

    /**
     * Returns the filename or null if the url path is a directory.
     *
     * @since 1.0.0
     *
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Returns the parent directory of the url path or null if url path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface|null The parent directory of the url path or null if url path does not have a parent directory.
     */
    public function getParentDirectory(): ?UrlPathInterface
    {
        if ($this->calculateParentDirectory($aboveBaseLevelCount, $directoryParts)) {
            return new self($this->isAbsolute, $aboveBaseLevelCount, $directoryParts, null);
        }

        return null;
    }

    /**
     * Returns true if url path has a parent directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path has a parent directory, false otherwise.
     */
    public function hasParentDirectory(): bool
    {
        return $this->isRelative() || count($this->directoryParts) > 0;
    }

    /**
     * Returns true if url path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is absolute, false otherwise.
     */
    public function isAbsolute(): bool
    {
        return $this->isAbsolute;
    }

    /**
     * Returns true if url path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a directory, false otherwise.
     */
    public function isDirectory(): bool
    {
        return $this->filename === null;
    }

    /**
     * Returns true if url path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is a file, false otherwise.
     */
    public function isFile(): bool
    {
        return $this->filename !== null;
    }

    /**
     * Returns true if url path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if url path is relative, false otherwise.
     */
    public function isRelative(): bool
    {
        return !$this->isAbsolute;
    }

    /**
     * Returns a copy of the url path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws UrlPathLogicException if the url path could not be made absolute.
     *
     * @return UrlPathInterface The url path as an absolute path.
     */
    public function toAbsolute(): UrlPathInterface
    {
        if ($this->aboveBaseLevelCount > 0) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->aboveBaseLevelCount, $this->directoryParts, $this->filename);
    }

    /**
     * Returns a copy of the url path as a relative path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative(): UrlPathInterface
    {
        return new self(false, $this->aboveBaseLevelCount, $this->directoryParts, $this->filename);
    }

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
    public function withUrlPath(UrlPathInterface $urlPath): UrlPathInterface
    {
        $result = new self(
            $this->isAbsolute,
            $this->aboveBaseLevelCount,
            $this->directoryParts,
            $urlPath->getFilename()
        );

        if (!$result->combineDirectory($urlPath->isAbsolute(), $urlPath->getDirectoryParts(), $error)) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be combined with url path "' . $urlPath->__toString() . '": ' . $error);
        }

        return $result;
    }

    /**
     * Returns a copy of the url path with another filename.
     *
     * @since 2.2.0
     *
     * @param string $filename The other filename
     *
     * @throws UrlPathInvalidArgumentException if the filename if invalid.
     *
     * @return UrlPathInterface The new url path.
     */
    public function withFilename(string $filename): UrlPathInterface
    {
        if (!self::validatePart($filename, false, $error)) {
            throw new UrlPathInvalidArgumentException($error);
        }

        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->directoryParts, $filename);
    }

    /**
     * Returns the url path as a string.
     *
     * @since 1.0.0
     *
     * @return string The url path as a string.
     */
    public function __toString(): string
    {
        $stringEncoder = function ($s) {
            return rawurlencode($s);
        };

        return $this->directoryToString(self::DIRECTORY_SEPARATOR, $stringEncoder) . $this->filenameToString($stringEncoder);
    }

    /**
     * Checks if a url path is valid.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @return bool True if the $path parameter is a valid url path, false otherwise.
     */
    public static function isValid(string $urlPath): bool
    {
        return self::doParse($urlPath) !== null;
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse(string $urlPath): UrlPathInterface
    {
        $result = self::doParse($urlPath, $error);
        if ($result === null) {
            throw new UrlPathInvalidArgumentException('Url path "' . $urlPath . '" is invalid: ' . $error);
        }

        return $result;
    }

    /**
     * Parses a url path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parseAsDirectory(string $urlPath): UrlPathInterface
    {
        $result = self::doParse($urlPath, $error);
        if ($result === null) {
            throw new UrlPathInvalidArgumentException('Url path "' . $urlPath . '" is invalid: ' . $error);
        }

        $result->convertToDirectory();

        return $result;
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParse(string $urlPath): ?UrlPathInterface
    {
        return self::doParse($urlPath);
    }

    /**
     * Parses a url path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParseAsDirectory(string $urlPath): ?UrlPathInterface
    {
        $result = self::doParse($urlPath);
        if ($result === null) {
            return null;
        }

        $result->convertToDirectory();

        return $result;
    }

    /**
     * Constructs a url path from value.
     *
     * @param bool        $isAbsolute     If true url path is absolute, if false url path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    private function __construct(bool $isAbsolute, int $aboveBaseLevel, array $directoryParts, ?string $filename)
    {
        $this->isAbsolute = $isAbsolute;
        $this->aboveBaseLevelCount = $aboveBaseLevel;
        $this->directoryParts = $directoryParts;
        $this->filename = $filename;
    }

    /**
     * Returns the directory as a string.
     *
     * @param string        $directorySeparator The directory separator.
     * @param callable|null $stringEncoder      The string encoding function or null if parts should not be encoded.
     *
     * @return string The directory as a string.
     */
    private function directoryToString(string $directorySeparator, ?callable $stringEncoder = null): string
    {
        $result = '';

        if ($this->aboveBaseLevelCount > 0) {
            $result .= str_repeat('..' . $directorySeparator, $this->aboveBaseLevelCount);
        }

        if ($this->isAbsolute) {
            $result .= $directorySeparator;
        }

        $result .= implode($directorySeparator, array_map($stringEncoder, $this->directoryParts));

        if (count($this->directoryParts) > 0) {
            $result .= $directorySeparator;
        }

        return $result;
    }

    /**
     * Returns the filename as a string.
     *
     * @param callable|null $stringEncoder The string encoding function or null if parts should not be encoded.
     *
     * @return string The filename as a string.
     */
    private function filenameToString(?callable $stringEncoder = null): string
    {
        if ($this->filename === null) {
            return '';
        }

        return $stringEncoder($this->filename);
    }

    /**
     * Tries to combine this with new directory info.
     *
     * @param bool        $isAbsolute     Whether the directory to combine with is absolute or relative.
     * @param string[]    $directoryParts The directory parts to combine with.
     * @param string|null $error          The error text if combining was not successful, undefined otherwise.
     *
     * @return bool True if combining was successful, false otherwise.
     */
    private function combineDirectory(bool $isAbsolute, array $directoryParts, ?string &$error): bool
    {
        if ($isAbsolute) {
            $this->isAbsolute = true;
            $this->aboveBaseLevelCount = 0;
            $this->directoryParts = $directoryParts;

            return true;
        }

        foreach ($directoryParts as $directoryPart) {
            if (!$this->addDirectoryPart($directoryPart, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tries to add a directory part to this.
     *
     * @param string      $part  The directory part.
     * @param string|null $error The error text if adding was not successful, undefined otherwise.
     *
     * @return bool True if adding was successful, false otherwise.
     */
    private function addDirectoryPart(string $part, ?string &$error): bool
    {
        if ($part === '..') {
            if (count($this->directoryParts) === 0) {
                if ($this->isAbsolute) {
                    $error = 'Absolute path is above root level.';

                    return false;
                }

                $this->aboveBaseLevelCount++;

                return true;
            }

            array_pop($this->directoryParts);

            return true;
        }

        $this->directoryParts[] = $part;

        return true;
    }

    /**
     * Tries to calculate the parent directory for this path and return the result.
     *
     * @param int|null      $aboveBaseLevelCount The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts      The directory parts if parsing was successful, undefined otherwise.
     *
     * @return bool True if this path has a parent directory, false otherwise.
     */
    private function calculateParentDirectory(?int &$aboveBaseLevelCount = null, ?array &$directoryParts = null): bool
    {
        if (count($this->directoryParts) > 0) {
            $aboveBaseLevelCount = $this->aboveBaseLevelCount;
            $directoryParts = array_slice($this->directoryParts, 0, -1);

            return true;
        }

        if ($this->isAbsolute) {
            return false;
        }

        $aboveBaseLevelCount = $this->aboveBaseLevelCount + 1;
        $directoryParts = $this->directoryParts;

        return true;
    }

    /**
     * Converts this path into a directory if this path is a file.
     */
    private function convertToDirectory(): void
    {
        if ($this->filename !== null) {
            $this->directoryParts[] = $this->filename;
            $this->filename = null;
        }
    }

    /**
     * Tries to parse an array of parts and returns the result or null.
     *
     * @param string[]    $parts          The parts.
     * @param bool        $isAbsolute     Whether the path is absolute or relative if parsing was successful, undefined otherwise.
     * @param int         $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]    $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null $filename       The filename if parsing was not successful, undefined otherwise.
     * @param string|null $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseParts(array $parts, bool &$isAbsolute, int &$aboveBaseLevel, array &$directoryParts, ?string &$filename, ?string &$error = null): bool
    {
        $partsCount = count($parts);

        foreach ($parts as $index => $part) {
            $part = $parts[$index];

            $isFirstPart = $index === 0;
            $isLastPart = $index === $partsCount - 1;

            if ($isFirstPart && $partsCount > 1 && $part === '') {
                // If the first part is empty and other parts follow, the path begins with directory separator and is therefore absolute.
                $isAbsolute = true;
                continue;
            }

            if (!self::parsePart($part, $isLastPart, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tries to parse a part of a path and returns the result or null.
     *
     * @param string      $part           The part of the path.
     * @param bool        $isLastPart     True if this is the last part, false otherwise.
     * @param bool        $isAbsolute     Whether the path is absolute or relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]    $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null $filename       The file if parsing was not successful, undefined otherwise.
     * @param string|null $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parsePart(string $part, bool $isLastPart, bool $isAbsolute, int &$aboveBaseLevel, array &$directoryParts, ?string &$filename, ?string &$error = null): bool
    {
        if ($part === '' || $part === '.') {
            return true;
        }

        if ($part === '..') {
            return self::parseParentDirectoryPart($isAbsolute, $aboveBaseLevel, $directoryParts, $error);
        }

        if (!$isLastPart) {
            return self::parseDirectoryPart($part, $directoryParts, $error);
        }

        return self::parseFilenamePart($part, $filename, $error);
    }

    /**
     * Tries to parse a parent directory part.
     *
     * @param bool        $isAbsolute     Whether the path is absolute or relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param array       $directoryParts The directory parts.
     * @param string|null $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseParentDirectoryPart(bool $isAbsolute, int &$aboveBaseLevel, array &$directoryParts, ?string &$error = null): bool
    {
        if (count($directoryParts) > 0) {
            array_pop($directoryParts);

            return true;
        }

        if ($isAbsolute) {
            $error = 'Absolute path is above root level.';

            return false;
        }

        $aboveBaseLevel++;

        return true;
    }

    /**
     * Parses a directory part.
     *
     * @param string      $part           The file name part.
     * @param array       $directoryParts The directory parts.
     * @param string|null $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseDirectoryPart(string $part, array &$directoryParts, ?string &$error = null): bool
    {
        if (!self::validatePart($part, true, $error)) {
            return false;
        }

        $directoryParts[] = self::decodePart($part);

        return true;
    }

    /**
     * Handles the file name part.
     *
     * @param string      $part     The file name part.
     * @param string|null $filename The file if parsing was not successful, undefined otherwise.
     * @param string|null $error    The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseFilenamePart(string $part, ?string &$filename = null, ?string &$error = null): bool
    {
        if (!self::validatePart($part, false, $error)) {
            return false;
        }

        $filename = self::decodePart($part);

        return true;
    }

    /**
     * Tries to parse a url path and returns the result or null.
     *
     * @param string      $str   The url path to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The url path if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        $parts = explode(self::DIRECTORY_SEPARATOR, $str);

        $isAbsolute = false;
        $aboveBaseLevel = 0;
        $directoryParts = [];
        $filename = null;

        if (!self::parseParts($parts, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Validates a directory part name or a file name.
     *
     * @param string      $part        The part to validate.
     * @param bool        $isDirectory If true part is a directory part name, if false part is a file name.
     * @param string|null $error       The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validatePart(string $part, bool $isDirectory, ?string &$error): bool
    {
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*+,;=:@\[\]%-]/', $part, $matches)) {
            $error = ($isDirectory ? 'Part of directory' : 'Filename') . ' "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Decodes a directory part or a file name.
     *
     * @param string $part The directory part or a file name.
     *
     * @return string The decoded directory part or file name.
     */
    private static function decodePart(string $part): string
    {
        return rawurldecode($part);
    }

    /**
     * @var int My number of directory parts above base level.
     */
    private $aboveBaseLevelCount;

    /**
     * @var string[] My directory parts.
     */
    private $directoryParts;

    /**
     * @var string|null My filename.
     */
    private $filename;

    /**
     * @var bool True if path is absolute, false otherwise.
     */
    private $isAbsolute;

    /**
     * @var string My directory separator.
     */
    private const DIRECTORY_SEPARATOR = '/';
}
