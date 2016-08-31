<?php

namespace DataTypes;

use DataTypes\Base\AbstractPath;
use DataTypes\Interfaces\FilePathInterface;

/**
 * Class representing a file path.
 */
class FilePath extends AbstractPath implements FilePathInterface
{
    /**
     * @return string The file path as a string.
     */
    public function __toString()
    {
        return $this->myToString(DIRECTORY_SEPARATOR);
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
        self::myParse(DIRECTORY_SEPARATOR, $filePath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename);

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Constructs a file path from values.
     *
     * @param bool        $isAbsolute     If true file path is absolute, if false file path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    protected function __construct($isAbsolute, $aboveBaseLevel, array $directoryParts, $filename = null)
    {
        parent::__construct($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }
}
