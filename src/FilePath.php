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
     * @return FilePathInterface The directory of the file path.
     */
    public function getDirectory()
    {
        return new self($this->myIsAbsolute, $this->myAboveBaseLevel, $this->myDrive, $this->myDirectoryParts, null);
    }

    /**
     * Returns the drive of the file path or null if no drive is present or supported.
     *
     * @since 1.0.0
     *
     * @return string|null The drive of the file path or null if no drive is present or supported.
     */
    public function getDrive()
    {
        return $this->myDrive;
    }

    /**
     * Returns the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePathInterface The file path as an absolute path.
     */
    public function toAbsolute()
    {
        if ($this->myAboveBaseLevel > 0) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->myAboveBaseLevel, $this->myDrive, $this->myDirectoryParts, $this->myFilename);
    }

    /**
     * Returns the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The file path as a relative path.
     */
    public function toRelative()
    {
        return new self(false, $this->myAboveBaseLevel, null, $this->myDirectoryParts, $this->myFilename);
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
     * @return FilePathInterface The combined file path.
     */
    public function withFilePath(FilePathInterface $filePath)
    {
        if (!$this->myCombine($filePath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be combined with file path "' . $filePath->__toString() . '": ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $filePath->getDrive() ?: $this->getDrive(), $directoryParts, $filename);
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
        return ($this->myDrive !== null ? $this->myDrive . ':' : '') . $this->myToString(DIRECTORY_SEPARATOR);
    }

    /**
     * Checks if a file path is valid.
     *
     * @since 1.0.0
     *
     * @param string $filePath The file path.
     *
     * @throws \InvalidArgumentException If the $filePath parameter is not a string.
     *
     * @return bool True if the $filePath parameter is a valid file path, false otherwise.
     */
    public static function isValid($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException('$filePath parameter is not a string.');
        }

        return self::myFilePathParse(
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
     * @throws \InvalidArgumentException        If the $filePath parameter is not a string.
     *
     * @return FilePathInterface The file path instance.
     */
    public static function parse($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException('$filePath parameter is not a string.');
        }

        if (!self::myFilePathParse(
            DIRECTORY_SEPARATOR,
            $filePath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            null,
            $isAbsolute,
            $aboveBaseLevel,
            $drive,
            $directoryParts,
            $filename,
            $error)
        ) {
            throw new FilePathInvalidArgumentException('File path "' . $filePath . '" is invalid: ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $drive, $directoryParts, $filename);
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
     *
     * @param string $filePath The file path.
     *
     * @throws \InvalidArgumentException If the $filePath parameter is not a string.
     *
     * @return FilePathInterface|null The file path instance if the $filePath parameter is a valid file path, null otherwise.
     */
    public static function tryParse($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException('$filePath parameter is not a string.');
        }

        if (!self::myFilePathParse(
            DIRECTORY_SEPARATOR,
            $filePath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            null,
            $isAbsolute,
            $aboveBaseLevel,
            $drive,
            $directoryParts,
            $filename)
        ) {
            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $drive, $directoryParts, $filename);
    }

    /**
     * Constructs a file path from values.
     *
     * @since 1.0.0
     *
     * @param bool        $isAbsolute     If true file path is absolute, if false file path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string|null $drive          The drive or null if no drive.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    private function __construct($isAbsolute, $aboveBaseLevel, $drive = null, array $directoryParts = [], $filename = null)
    {
        $this->myIsAbsolute = $isAbsolute;
        $this->myAboveBaseLevel = $aboveBaseLevel;
        $this->myDrive = $drive;
        $this->myDirectoryParts = $directoryParts;
        $this->myFilename = $filename;
    }

    /**
     * Tries to parse a file path and returns the result or error text.
     *
     * @param string        $directorySeparator The directory separator.
     * @param string        $path               The path.
     * @param callable      $partValidator      The part validator.
     * @param callable      $stringDecoder      The string decoding function or null if parts should not be decoded.
     * @param bool|null     $isAbsolute         Whether the path is absolute or relative is parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel     The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string|null   $drive              The drive or null if parsing was successful, undefined otherwise
     * @param string[]|null $directoryParts     The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename           The file or null if parsing was not successful, undefined otherwise.
     * @param string|null   $error              The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myFilePathParse($directorySeparator, $path, callable $partValidator, callable $stringDecoder = null, &$isAbsolute = null, &$aboveBaseLevel = null, &$drive = null, array &$directoryParts = null, &$filename = null, &$error = null)
    {
        $drive = null;

        // If on Window, try to parse drive.
        if (self::myIsWindows()) {
            $driveAndPath = explode(':', $path, 2);

            if (count($driveAndPath) === 2) {
                $drive = $driveAndPath[0];
                if (!self::myDriveValidator($drive, $error)) {
                    return false;
                }

                $path = $driveAndPath[1];
            }
        }

        $result = self::myParse(
            $directorySeparator,
            DIRECTORY_SEPARATOR !== '\'' ? str_replace('/', DIRECTORY_SEPARATOR, $path) : $path,
            $partValidator,
            $stringDecoder,
            $isAbsolute,
            $aboveBaseLevel,
            $directoryParts,
            $filename,
            $error
        );

        // File path containing a drive and relative path is invalid.
        if ($drive !== null && !$isAbsolute) {
            $error = 'Path can not contain drive "' . $drive . '" and non-absolute path "' . $path . '".';

            return false;
        }

        return $result;
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
        if (preg_match(self::myIsWindows() ? '/[\0<>:*?"|]+/' : '/[\0]+/', $part, $matches)) {
            $error = ($isDirectory ? 'Part of directory' : 'Filename') . ' "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates a drive.
     *
     * @param string $drive The drive to validate.
     * @param string $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myDriveValidator($drive, &$error)
    {
        if (!preg_match('/^[a-zA-Z]$/', $drive)) {
            $error = 'Drive "' . $drive . '" is invalid.';

            return false;
        }

        return true;
    }

    /**
     * Returns true if the operating system is windows, false otherwise.
     *
     * @return bool True if the operating system is windows, false otherwise.
     */
    private static function myIsWindows()
    {
        return strtolower(substr(php_uname('s'), 0, 7)) === 'windows';
    }

    /**
     * @var string|null My drive or null if no drive is present or supported.
     */
    private $myDrive;
}
