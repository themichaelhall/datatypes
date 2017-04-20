<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

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
    public function getDepth()
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
    public function getDirectoryParts()
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
    public function getFilename()
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
    public function hasParentDirectory()
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
    public function isAbsolute()
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
    public function isDirectory()
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
    public function isFile()
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
    public function isRelative()
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
    private function myToString($directorySeparator, callable $stringEncoder = null)
    {
        // If above base level (for relative path), append the required number of "../".
        $result = $this->myAboveBaseLevel > 0 ? str_repeat('..' . $directorySeparator, $this->myAboveBaseLevel) : '';

        // Directory parts.
        $result .= $this->myIsAbsolute ? $directorySeparator : '';
        if ($stringEncoder !== null) {
            $result .= implode($directorySeparator, array_map($stringEncoder, $this->myDirectoryParts));
        } else {
            $result .= implode($directorySeparator, $this->myDirectoryParts);
        }
        $result .= count($this->myDirectoryParts) > 0 ? $directorySeparator : '';

        // File part.
        if ($stringEncoder !== null) {
            $result .= ($this->myFilename !== null ? $stringEncoder($this->myFilename) : '');
        } else {
            $result .= ($this->myFilename !== null ? $this->myFilename : '');
        }

        return $result;
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
    private function myCombine(PathTraitInterface $other, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null, &$error = null)
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
            if ($otherDirectoryPart === '..') {
                if (count($directoryParts) === 0) {
                    if ($this->myIsAbsolute) {
                        $error = 'Absolute path is above root level.';

                        return false;
                    }

                    ++$aboveBaseLevel;
                } else {
                    array_pop($directoryParts);
                }
            } else {
                $directoryParts[] = $otherDirectoryPart;
            }
        }

        return true;
    }

    /**
     * Tries to parse a path and returns the result or error text.
     *
     * @param string        $directorySeparator The directory separator.
     * @param string        $path               The path.
     * @param callable      $partValidator      The part validator.
     * @param callable      $stringDecoder      The string decoding function or null if parts should not be decoded.
     * @param bool|null     $isAbsolute         Whether the path is absolute or relative is parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel     The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts     The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename           The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error              The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($directorySeparator, $path, callable $partValidator, callable $stringDecoder = null, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null, &$error = null)
    {
        $parts = explode($directorySeparator, $path);
        $partsCount = count($parts);

        $directoryParts = [];
        $filename = null;
        $isAbsolute = false;
        $aboveBaseLevel = 0;

        // Parse the directories
        for ($i = 0; $i < $partsCount; ++$i) {
            $part = $parts[$i];

            // If the first part is empty and other parts follow, the path begins with directory separator and is therefore absolute.
            if ($i === 0 && $part === '' && $partsCount > 1) {
                $isAbsolute = true;

                continue;
            }

            // If part is empty, the path contains continuous directory separators and should be skipped.
            if ($part === '') {
                continue;
            }

            // Handle "current directory"-part.
            if ($part === '.') {
                continue;
            }

            // Handle "parent directory"-part.
            if ($part === '..') {
                if (count($directoryParts) === 0) {
                    if ($isAbsolute) {
                        $error = 'Absolute path is above root level.';

                        return false;
                    }

                    ++$aboveBaseLevel;
                } else {
                    array_pop($directoryParts);
                }

                continue;
            }

            if ($i === $partsCount - 1) {
                // This is the last part (i.e. the filename part).
                if ($part !== '') {
                    if (!$partValidator($part, false, $error)) {
                        return false;
                    }

                    if ($stringDecoder !== null) {
                        $part = $stringDecoder($part);
                    }

                    $filename = $part;
                }
            } else {
                // This is a directory part.
                if (!$partValidator($part, true, $error)) {
                    return false;
                }

                if ($stringDecoder !== null) {
                    $part = $stringDecoder($part);
                }

                $directoryParts[] = $part;
            }
        }

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
