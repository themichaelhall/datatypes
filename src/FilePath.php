<?php

namespace DataTypes;

use DataTypes\Base\AbstractPath;
use DataTypes\Exceptions\FilePathInvalidArgumentException;
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
     * @throws FilePathInvalidArgumentException If the $filePath parameter is not a valid file path.
     *
     * @return FilePath The file path instance.
     */
    public static function parse($filePath)
    {
        assert(is_string($filePath), '$filePath is not a string');

        $validatePartFunction = function ($p, $d, &$e) {
            return self::myPartValidator($p, $d, $e);
        };

        if (!self::myParse(DIRECTORY_SEPARATOR, $filePath, $validatePartFunction, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)
        ) {
            throw new FilePathInvalidArgumentException('File path "' . $filePath . '" is invalid: ' . $error);
        }

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

    /**
     * Validates a directory part name or a file name.
     *
     * @param string $part        The part to validate.
     * @param bool   $isDirectory If true part is a directory part name, if false part is a file name.
     * @param string $error       The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myPartValidator($part, $isDirectory, &$error)
    {
        // fixme: More specific validation depending on the operating system.
        if ($isDirectory) {
            if (preg_match('/[\0]+/', $part, $matches)) {
                $error = 'Part of directory "' . $part . '" contains invalid character "' . $matches[0] . '".';

                return false;
            }
        }

        return true;
    }
}
