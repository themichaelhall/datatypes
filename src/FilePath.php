<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
namespace DataTypes;

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\Interfaces\FilePathInterface;
use DataTypes\Traits\PathTrait;

/**
 * Class representing a file path.
 *
 * @since 1.0.0
 */
class FilePath implements FilePathInterface
{
    use PathTrait;

    /**
     * Returns the directory of the file path.
     *
     * @since 1.0.0
     *
     * @return FilePath The directory of the file path.
     */
    public function getDirectory()
    {
        return new self($this->myIsAbsolute, $this->myAboveBaseLevel, $this->myDirectoryParts, null);
    }

    /**
     * Returns the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePath The file path as an absolute path.
     */
    public function toAbsolute()
    {
        if ($this->myAboveBaseLevel > 0) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->myAboveBaseLevel, $this->myDirectoryParts, $this->myFilename);
    }

    /**
     * Returns the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePath The file path as a relative path.
     */
    public function toRelative()
    {
        return new self(false, $this->myAboveBaseLevel, $this->myDirectoryParts, $this->myFilename);
    }

    /**
     * Returns a copy of the file path combined with another file path.
     *
     * @since 1.0.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @throws FilePathLogicException if the file paths could not be combined.
     *
     * @return FilePath The combined file path.
     */
    public function withFilePath(FilePathInterface $filePath)
    {
        if (!$this->myCombine($filePath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be combined with file path "' . $filePath->__toString() . '": ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Returns the file path as a string.
     *
     * @since 1.0.0
     *
     * @return string The file path as a string.
     */
    public function __toString()
    {
        return $this->myToString(DIRECTORY_SEPARATOR);
    }

    /**
     * Checks if a file path is valid.
     *
     * @since 1.0.0
     *
     * @param string $filePath The file path.
     *
     * @return bool True if the $filePath parameter is a valid file path, false otherwise.
     */
    public static function isValid($filePath)
    {
        assert(is_string($filePath), '$filePath is not a string');

        return self::myParse(
            DIRECTORY_SEPARATOR,
            $filePath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            });
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
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

        if (!self::myParse(
            DIRECTORY_SEPARATOR,
            $filePath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            $isAbsolute,
            $aboveBaseLevel,
            $directoryParts,
            $filename,
            $error)
        ) {
            throw new FilePathInvalidArgumentException('File path "' . $filePath . '" is invalid: ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
     *
     * @param string $filePath The file path.
     *
     * @return FilePath|null The file path instance if the $filePath parameter is a valid file path, null otherwise.
     */
    public static function tryParse($filePath)
    {
        assert(is_string($filePath), '$filePath is not a string');

        if (!self::myParse(
            DIRECTORY_SEPARATOR,
            $filePath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            $isAbsolute,
            $aboveBaseLevel,
            $directoryParts,
            $filename)
        ) {
            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Constructs a file path from values.
     *
     * @since 1.0.0
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
        } else {
            if (preg_match('/[\0]+/', $part, $matches)) {
                $error = 'Filename "' . $part . '" contains invalid character "' . $matches[0] . '".';

                return false;
            }
        }

        return true;
    }
}
