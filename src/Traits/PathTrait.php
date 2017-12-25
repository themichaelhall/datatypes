<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
declare(strict_types=1);

namespace DataTypes\Traits;

use DataTypes\Interfaces\Traits\PathTraitInterface;

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
        return count($this->myDirectoryParts) - $this->myAboveBaseLevel;
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
        return $this->myAboveBaseLevel === 0 ? $this->myDirectoryParts : array_merge(array_fill(0, $this->myAboveBaseLevel, '..'), $this->myDirectoryParts);
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
        return $this->myFilename;
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
        return $this->isRelative() || count($this->myDirectoryParts) > 0;
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
        return $this->myIsAbsolute;
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
        return $this->myFilename === null;
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
        return $this->myFilename !== null;
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
        return !$this->myIsAbsolute;
    }

    /**
     * Returns the path as a string.
     *
     * @param string        $directorySeparator The directory separator.
     * @param callable|null $stringEncoder      The string encoding function or null if parts should not be encoded.
     *
     * @return string The path as a string.
     */
    private function myToString(string $directorySeparator, ?callable $stringEncoder = null): string
    {
        return $this->myDirectoryToString($directorySeparator, $stringEncoder) . $this->myFilenameToString($stringEncoder);
    }

    /**
     * Returns the directory as a string.
     *
     * @param string        $directorySeparator The directory separator.
     * @param callable|null $stringEncoder      The string encoding function or null if parts should not be encoded.
     *
     * @return string The directory as a string.
     */
    private function myDirectoryToString(string $directorySeparator, ?callable $stringEncoder = null): string
    {
        $result = '';

        if ($this->myAboveBaseLevel > 0) {
            $result .= str_repeat('..' . $directorySeparator, $this->myAboveBaseLevel);
        }

        if ($this->myIsAbsolute) {
            $result .= $directorySeparator;
        }

        $result .= implode($directorySeparator, $stringEncoder !== null ?
            array_map($stringEncoder, $this->myDirectoryParts) :
            $this->myDirectoryParts);

        if (count($this->myDirectoryParts) > 0) {
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
    private function myFilenameToString(?callable $stringEncoder = null): string
    {
        if ($this->myFilename === null) {
            return '';
        }

        if ($stringEncoder !== null) {
            return $stringEncoder($this->myFilename);
        }

        return $this->myFilename;
    }

    /**
     * Tries to combine this path with another path.
     *
     * @param PathTraitInterface $other          The other path.
     * @param bool|null          $isAbsolute     Whether the path is absolute or relative is combining was successful, undefined otherwise.
     * @param int|null           $aboveBaseLevel The number of directory parts above base level if combining was successful, undefined otherwise.
     * @param string[]|null      $directoryParts The directory parts if combining was successful, undefined otherwise.
     * @param string|null        $filename       The file if combining was not successful, undefined otherwise.
     * @param string|null        $error          The error text if combining was not successful, undefined otherwise.
     *
     * @return bool True if combining was successful, false otherwise.
     */
    private function myCombine(PathTraitInterface $other, ?bool &$isAbsolute = null, ?int &$aboveBaseLevel = null, ?array &$directoryParts = null, ?string &$filename = null, ?string &$error = null): bool
    {
        // If other path is absolute, current path is overridden.
        if ($other->isAbsolute()) {
            $isAbsolute = $other->isAbsolute();
            $aboveBaseLevel = 0;
            $directoryParts = $other->getDirectoryParts();
            $filename = $other->getFilename();

            return true;
        }

        $isAbsolute = $this->myIsAbsolute;
        $aboveBaseLevel = $this->myAboveBaseLevel;
        $directoryParts = $this->myDirectoryParts;
        $filename = $other->getFilename();

        foreach ($other->getDirectoryParts() as $otherDirectoryPart) {
            if (!$this->myCombineDirectoryPart($otherDirectoryPart, $aboveBaseLevel, $directoryParts, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tries to combine a directory part with another path.
     *
     * @param string        $part           The part.
     * @param int|null      $aboveBaseLevel The number of directory parts above base level if combining was successful, undefined otherwise.
     * @param string[]|null $directoryParts The directory parts if combining was successful, undefined otherwise.
     * @param string|null   $error          The error text if combining was not successful, undefined otherwise.
     *
     * @return bool True if combining was successful, false otherwise.
     */
    private function myCombineDirectoryPart(string $part, ?int &$aboveBaseLevel = null, ?array &$directoryParts = null, ?string &$error = null): bool
    {
        if ($part === '..') {
            if (count($directoryParts) === 0) {
                if ($this->myIsAbsolute) {
                    $error = 'Absolute path is above root level.';

                    return false;
                }

                $aboveBaseLevel++;

                return true;
            }

            array_pop($directoryParts);

            return true;
        }

        $directoryParts[] = $part;

        return true;
    }

    /**
     * Tries to calculate the parent directory for this path and return the result.
     *
     * @param int|null      $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts The directory parts if parsing was successful, undefined otherwise.
     *
     * @return bool True if this path has a parent directory, false otherwise.
     */
    private function myParentDirectory(?int &$aboveBaseLevel = null, ?array &$directoryParts = null): bool
    {
        if (count($this->myDirectoryParts) > 0) {
            $aboveBaseLevel = $this->myAboveBaseLevel;
            $directoryParts = array_slice($this->myDirectoryParts, 0, -1);

            return true;
        }

        if ($this->myIsAbsolute) {
            return false;
        }

        $aboveBaseLevel = $this->myAboveBaseLevel + 1;
        $directoryParts = $this->myDirectoryParts;

        return true;
    }

    /**
     * Tries to parse a path and returns the result or error text.
     *
     * @param string        $directorySeparator The directory separator.
     * @param string        $path               The path.
     * @param callable      $partValidator      The part validator.
     * @param callable|null $stringDecoder      The string decoding function or null if parts should not be decoded.
     * @param bool|null     $isAbsolute         Whether the path is absolute or relative if parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel     The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts     The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename           The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error              The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse(string $directorySeparator, string $path, callable $partValidator, ?callable $stringDecoder = null, ?bool &$isAbsolute = null, ?int &$aboveBaseLevel = null, ?array &$directoryParts = null, ?string &$filename = null, ?string &$error = null): bool
    {
        $parts = explode($directorySeparator, $path);
        $partsCount = count($parts);

        $directoryParts = [];
        $filename = null;
        $isAbsolute = false;
        $aboveBaseLevel = 0;
        $index = 0;

        // If the first part is empty and other parts follow, the path begins with directory separator and is therefore absolute.
        if ($partsCount > 1 && $parts[0] === '') {
            $isAbsolute = true;
            $index++;
        }

        // Go through all parts.
        for (; $index < $partsCount; $index++) {
            if (!self::myParsePart($parts[$index], $index === $partsCount - 1, $partValidator, $stringDecoder, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tries to parse a part of a path and returns the result or error text.
     *
     * @param string        $part           The part of the path.
     * @param bool          $isLastPart     True if this is the last part, false otherwise.
     * @param callable      $partValidator  The part validator.
     * @param callable|null $stringDecoder  The string decoding function or null if parts should not be decoded.
     * @param bool|null     $isAbsolute     Whether the path is absolute or relative if parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename       The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParsePart(string $part, bool $isLastPart, callable $partValidator, ?callable $stringDecoder, ?bool $isAbsolute, ?int &$aboveBaseLevel, ?array &$directoryParts = null, ?string &$filename = null, ?string &$error = null): bool
    {
        // Skip empty and current directory parts.
        if ($part === '' || $part === '.') {
            return true;
        }

        // Handle parent directory-part.
        if ($part === '..') {
            return self::myHandleParentDirectoryPart($isAbsolute, $aboveBaseLevel, $directoryParts, $error);
        }

        // Handle directory part.
        if (!$isLastPart) {
            return self::myHandleDirectoryPart($part, $partValidator, $stringDecoder, $directoryParts, $error);
        }

        // Handle last (i.e. filename) part.
        return self::myHandleFilenamePart($part, $partValidator, $stringDecoder, $filename, $error);
    }

    /**
     * Handles a parent directory part.
     *
     * @param bool        $isAbsolute     Whether the path is absolute or relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param array       $directoryParts The directory parts.
     * @param string|null $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myHandleParentDirectoryPart(bool $isAbsolute, int &$aboveBaseLevel, array &$directoryParts, ?string &$error = null): bool
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
     * Handles the file name part.
     *
     * @param string        $part          The file name part.
     * @param callable      $partValidator The part validator.
     * @param callable|null $stringDecoder The string decoding function or null if parts should not be decoded.
     * @param string|null   $filename      The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error         The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myHandleFilenamePart(string $part, callable $partValidator, ?callable $stringDecoder = null, ?string &$filename = null, ?string &$error = null): bool
    {
        if (!$partValidator($part, false, $error)) {
            return false;
        }

        if ($stringDecoder !== null) {
            $part = $stringDecoder($part);
        }

        $filename = $part;

        return true;
    }

    /**
     * Handles the directory part.
     *
     * @param string        $part           The file name part.
     * @param callable      $partValidator  The part validator.
     * @param callable|null $stringDecoder  The string decoding function or null if parts should not be decoded.
     * @param array         $directoryParts The directory parts.
     * @param string|null   $error          The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myHandleDirectoryPart(string $part, callable $partValidator, ?callable $stringDecoder = null, ?array &$directoryParts, ?string &$error = null): bool
    {
        if (!$partValidator($part, true, $error)) {
            return false;
        }

        if ($stringDecoder !== null) {
            $part = $stringDecoder($part);
        }

        $directoryParts[] = $part;

        return true;
    }

    /**
     * @var int My number of directory parts above base level.
     */
    private $myAboveBaseLevel;

    /**
     * @var string[] My directory parts.
     */
    private $myDirectoryParts;

    /**
     * @var string|null My filename.
     */
    private $myFilename;

    /**
     * @var bool True if path is absolute, false otherwise.
     */
    private $myIsAbsolute;
}
