<?php

namespace DataTypes\Base;

/**
 * Abstract class representing a path.
 */
abstract class AbstractPath
{
    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts()
    {
        return $this->myAboveBaseLevel === 0 ? $this->myDirectoryParts : array_merge(array_fill(0, $this->myAboveBaseLevel, '..'), $this->myDirectoryParts);
    }

    /**
     * @return string|null The filename or null if the path is a directory.
     */
    public function getFilename()
    {
        return $this->myFilename;
    }

    /**
     * @return bool True if path is absolute, false otherwise.
     */
    public function isAbsolute()
    {
        return $this->myIsAbsolute;
    }

    /**
     * @return bool True if path is relative, false otherwise.
     */
    public function isRelative()
    {
        return !$this->myIsAbsolute;
    }

    /**
     * Returns the path as a string.
     *
     * @param string $directorySeparator The directory separator.
     *
     * @return string The path as a string.
     */
    protected function myToString($directorySeparator)
    {
        return
            // Directory parts.
            ($this->myIsAbsolute ? $directorySeparator : '') . implode($directorySeparator, $this->myDirectoryParts) . (count($this->myDirectoryParts) > 0 ? $directorySeparator : '') .
            // File part.
            ($this->myFilename !== null ? $this->myFilename : '');
    }

    /**
     * Constructs a path from values.
     *
     * @param bool        $isAbsolute     If true path is absolute, if false path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    protected function __construct($isAbsolute, $aboveBaseLevel, array $directoryParts, $filename = null)
    {
        $this->myIsAbsolute = $isAbsolute;
        $this->myAboveBaseLevel = $aboveBaseLevel;
        $this->myDirectoryParts = $directoryParts;
        $this->myFilename = $filename;
    }

    /**
     * Tries to parse an file path and returns the result or error text.
     *
     * @param string        $directorySeparator The directory separator.
     * @param string        $path               The path.
     * @param bool|null     $isAbsolute         Whether the path is absolute or relative is parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel     The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts     The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename           The file if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    protected static function myParse($directorySeparator, $path, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null)
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
    protected $myAboveBaseLevel;

    /**
     * @var string My directory parts.
     */
    protected $myDirectoryParts;

    /**
     * @var string|null My filename.
     */
    protected $myFilename;

    /**
     * @var bool True if path is absolute, false otherwise.
     */
    protected $myIsAbsolute;
}
