<?php
/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */
declare(strict_types=1);

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
     * Returns true if the file path equals other file path, false otherwise.
     *
     * @since 1.2.0
     *
     * @param FilePathInterface $filePath The other file path.
     *
     * @return bool True if the file path equals other file path, false otherwise.
     */
    public function equals(FilePathInterface $filePath): bool
    {
        return $this->getDrive() === $filePath->getDrive() && $this->isAbsolute() === $filePath->isAbsolute() && $this->getDirectoryParts() === $filePath->getDirectoryParts() && $this->getFilename() === $filePath->getFilename();
    }

    /**
     * Returns the directory of the file path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The directory of the file path.
     */
    public function getDirectory(): FilePathInterface
    {
        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->drive, $this->directoryParts, null);
    }

    /**
     * Returns the drive of the file path or null if no drive is present or supported.
     *
     * @since 1.0.0
     *
     * @return string|null The drive of the file path or null if no drive is present or supported.
     */
    public function getDrive(): ?string
    {
        return $this->drive;
    }

    /**
     * Returns the parent directory of the file path or null if file path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface|null The parent directory of the file path or null if file path does not have a parent directory.
     */
    public function getParentDirectory(): ?FilePathInterface
    {
        if ($this->calculateParentDirectory($aboveBaseLevelCount, $directoryParts)) {
            return new self($this->isAbsolute, $aboveBaseLevelCount, $this->drive, $directoryParts, null);
        }

        return null;
    }

    /**
     * Returns a copy of the file path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws FilePathLogicException if the file path could not be made absolute.
     *
     * @return FilePathInterface The file path as an absolute path.
     */
    public function toAbsolute(): FilePathInterface
    {
        if ($this->aboveBaseLevelCount > 0) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->aboveBaseLevelCount, $this->drive, $this->directoryParts, $this->filename);
    }

    /**
     * Returns a copy of the file path as a relative path.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface The file path as a relative path.
     */
    public function toRelative(): FilePathInterface
    {
        return new self(false, $this->aboveBaseLevelCount, null, $this->directoryParts, $this->filename);
    }

    /**
     * Returns a copy of the file path with another filename.
     *
     * @since 2.2.0
     *
     * @param string $filename The other filename
     *
     * @throws FilePathInvalidArgumentException if the filename if invalid.
     *
     * @return FilePathInterface The new file path.
     */
    public function withFilename(string $filename): FilePathInterface
    {
        if (!self::validatePart($filename, false, $error)) {
            throw new FilePathInvalidArgumentException($error);
        }

        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->drive, $this->directoryParts, $filename);
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
    public function withFilePath(FilePathInterface $filePath): FilePathInterface
    {
        $result = new self(
            $this->isAbsolute,
            $this->aboveBaseLevelCount,
            $filePath->getDrive() ?: $this->getDrive(),
            $this->directoryParts,
            $filePath->getFilename()
        );

        if (!$result->combineDirectory($filePath->isAbsolute(), $filePath->getDirectoryParts(), $error)) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be combined with file path "' . $filePath->__toString() . '": ' . $error);
        }

        return $result;
    }

    /**
     * Returns the file path as a string.
     *
     * @since 1.0.0
     *
     * @return string The file path as a string.
     */
    public function __toString(): string
    {
        return ($this->drive !== null ? $this->drive . ':' : '') . $this->toString(DIRECTORY_SEPARATOR);
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
    public static function isValid(string $filePath): bool
    {
        return self::doParse($filePath) !== null;
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
     * @return FilePathInterface The file path instance.
     */
    public static function parse(string $filePath): FilePathInterface
    {
        $result = self::doParse($filePath, $error);
        if ($result === null) {
            throw new FilePathInvalidArgumentException('File path "' . $filePath . '" is invalid: ' . $error);
        }

        return $result;
    }

    /**
     * Parses a file path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $filePath The file path.
     *
     * @throws FilePathInvalidArgumentException If the $filePath parameter is not a valid file path.
     *
     * @return FilePathInterface The file path instance.
     */
    public static function parseAsDirectory(string $filePath): FilePathInterface
    {
        $result = self::doParse($filePath, $error);
        if ($result === null) {
            throw new FilePathInvalidArgumentException('File path "' . $filePath . '" is invalid: ' . $error);
        }

        $result->convertToDirectory();

        return $result;
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
     *
     * @param string $filePath The file path.
     *
     * @return FilePathInterface|null The file path instance if the $filePath parameter is a valid file path, null otherwise.
     */
    public static function tryParse(string $filePath): ?FilePathInterface
    {
        return self::doParse($filePath);
    }

    /**
     * Parses a file path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $filePath The file path.
     *
     * @return FilePathInterface|null The file path instance if the $filePath parameter is a valid file path, null otherwise.
     */
    public static function tryParseAsDirectory(string $filePath): ?FilePathInterface
    {
        $result = self::doParse($filePath);
        if ($result === null) {
            return null;
        }

        $result->convertToDirectory();

        return $result;
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
    private function __construct(bool $isAbsolute, int $aboveBaseLevel, ?string $drive, array $directoryParts, ?string $filename)
    {
        $this->isAbsolute = $isAbsolute;
        $this->aboveBaseLevelCount = $aboveBaseLevel;
        $this->drive = $drive;
        $this->directoryParts = $directoryParts;
        $this->filename = $filename;
    }

    /**
     * Tries to parse a file path and returns the result or null.
     *
     * @param string      $str   The file path to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The file path if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        $drive = null;

        // If on Window, try to parse drive.
        if (self::isWindows()) {
            $driveAndPath = explode(':', $str, 2);

            if (count($driveAndPath) === 2) {
                $drive = $driveAndPath[0];
                if (!self::validateDrive($drive, $error)) {
                    return null;
                }

                $drive = strtoupper($drive);
                $str = $driveAndPath[1];
            }
        }

        $parts = explode(
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR !== '/' ? str_replace('/', DIRECTORY_SEPARATOR, $str) : $str
        );

        $isAbsolute = false;
        $aboveBaseLevel = 0;
        $directoryParts = [];
        $filename = null;

        if (!self::parseParts($parts, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            return null;
        }

        if ($drive !== null && !$isAbsolute) {
            $error = 'Path can not contain drive "' . $drive . '" and non-absolute path "' . $str . '".';

            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $drive, $directoryParts, $filename);
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
        if (preg_match(self::isWindows() ? '/[\0<>:*?"|\\/\\\\]+/' : '/[\0\\/]+/', $part, $matches)) {
            $error = ($isDirectory ? 'Part of directory' : 'Filename') . ' "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates a drive.
     *
     * @param string      $drive The drive to validate.
     * @param string|null $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateDrive(string $drive, ?string &$error): bool
    {
        if (!preg_match('/^[a-zA-Z]$/', $drive)) {
            $error = 'Drive "' . $drive . '" is invalid.';

            return false;
        }

        return true;
    }

    /**
     * Decodes a directory part or a file name. Does nothing for this class.
     *
     * @param string $part The directory part or a file name.
     *
     * @return string The decoded directory part or file name.
     */
    private static function decodePart(string $part): string
    {
        return $part;
    }

    /**
     * Returns true if the operating system is windows, false otherwise.
     *
     * @return bool True if the operating system is windows, false otherwise.
     */
    private static function isWindows(): bool
    {
        return strtolower(substr(php_uname('s'), 0, 7)) === 'windows';
    }

    /**
     * @var string|null My drive or null if no drive is present or supported.
     */
    private $drive;
}
