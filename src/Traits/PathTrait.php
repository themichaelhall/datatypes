<?php
/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */
declare(strict_types=1);

namespace DataTypes\Traits;

/**
 * Trait representing a path.
 *
 * @since 1.0.0
 */
trait PathTrait
{
    /**
     * Returns the depth of the path.
     *
     * @since 1.0.0
     *
     * @return int The depth of the path.
     */
    public function getDepth(): int
    {
        return count($this->directoryParts) - $this->aboveBaseLevelCount;
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
     * Returns the filename or null if the path is a directory.
     *
     * @since 1.0.0
     *
     * @return string|null The filename or null if the path is a directory.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Returns true if path has a parent directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path has a parent directory, false otherwise.
     */
    public function hasParentDirectory(): bool
    {
        return $this->isRelative() || count($this->directoryParts) > 0;
    }

    /**
     * Returns true if path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is absolute, false otherwise.
     */
    public function isAbsolute(): bool
    {
        return $this->isAbsolute;
    }

    /**
     * Returns true if path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is a directory, false otherwise.
     */
    public function isDirectory(): bool
    {
        return $this->filename === null;
    }

    /**
     * Returns true if path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is a file, false otherwise.
     */
    public function isFile(): bool
    {
        return $this->filename !== null;
    }

    /**
     * Returns true if path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if path is relative, false otherwise.
     */
    public function isRelative(): bool
    {
        return !$this->isAbsolute;
    }

    /**
     * Returns the path as a string.
     *
     * @param string        $directorySeparator The directory separator.
     * @param callable|null $stringEncoder      The string encoding function or null if parts should not be encoded.
     *
     * @return string The path as a string.
     */
    private function toString(string $directorySeparator, ?callable $stringEncoder = null): string
    {
        return $this->directoryToString($directorySeparator, $stringEncoder) . $this->filenameToString($stringEncoder);
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

        $result .= implode($directorySeparator, $stringEncoder !== null ?
            array_map($stringEncoder, $this->directoryParts) :
            $this->directoryParts);

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

        if ($stringEncoder !== null) {
            return $stringEncoder($this->filename);
        }

        return $this->filename;
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
}
