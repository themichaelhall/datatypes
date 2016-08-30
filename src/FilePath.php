<?php

namespace DataTypes;

use DataTypes\Interfaces\FilePathInterface;

/**
 * Class representing a file path.
 */
class FilePath implements FilePathInterface
{
    /**
     * @return string The file path as a string.
     */
    public function __toString()
    {
        return
            // Directory parts.
            ($this->myIsAbsolute ? DIRECTORY_SEPARATOR : '') . implode(DIRECTORY_SEPARATOR, $this->myDirectoryParts) . (count($this->myDirectoryParts) > 0 ? DIRECTORY_SEPARATOR : '') .
            // File part.
            ($this->myFilename !== null ? $this->myFilename : '');
    }

    /**
     * Parses a file path.
     *
     * @param string $filePath The file path.
     *
     * @return FilePath The file path instance.
     */
    public static function parse($filePath)
    {
        assert(is_string($filePath), '$filePath is not a string');
        static::myParse($filePath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename);

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Constructs a file path from value.
     *
     * @param bool        $isAbsolute     If true file path is absolute, if false file path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    private function __construct($isAbsolute, $aboveBaseLevel, array $directoryParts, $filename = null)
    {
        $this->myIsAbsolute = $isAbsolute;
        $this->myAboveBaseLevel = $aboveBaseLevel;
        $this->myDirectoryParts = $directoryParts;
        $this->myFilename = $filename;
    }

    /**
     * Tries to parse an file path and returns the result or error text.
     *
     * @param string        $filePath       The file path.
     * @param bool|null     $isAbsolute     Whether the path is absolute or relative is parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename       The file if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($filePath, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $filePath);
        $partsCount = count($parts);

        $directoryParts = [];
        $filename = null;
        $isAbsolute = false;
        $aboveBaseLevel = 0;

        // Parse the directories
        for ($i = 0; $i < $partsCount; ++$i) {
            $part = $parts[$i];

            // If the first part is empty and other parts follow, the path begins with DIRECTORY_SEPARATOR and is therefore absolute.
            if ($i === 0 && $part === '' && $partsCount > 1) {
                $isAbsolute = true;

                continue;
            }

            // If part is empty, the path contains DIRECTORY_SEPARATOR and should be skipped.
            if ($part === '') {
                continue;
            }

            if ($i === $partsCount - 1) {
                // This is the last part (i.e. the filename part).
                if ($part !== '') {
                    $filename = $part;
                }
            } else {
                // This is a directory part.
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
     * @var string My directory parts.
     */
    private $myDirectoryParts;

    /**
     * @var string|null My filename.
     */
    private $myFilename;

    /**
     * @var bool True if file path is absolute, false otherwise.
     */
    private $myIsAbsolute;
}
