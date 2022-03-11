<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\System;

use DataTypes\System\Exceptions\FilePathInvalidArgumentException;
use DataTypes\System\Exceptions\FilePathLogicException;

/**
 * Class representing a file path.
 *
 * @since 1.0.0
 */
class FilePath implements FilePathInterface
{
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
     * Returns the parent directory of the file path or null if file path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return FilePathInterface|null The parent directory of the file path or null if file path does not have a parent directory.
     */
    public function getParentDirectory(): ?FilePathInterface
    {
        if (!$this->hasParentDirectory()) {
            return null;
        }

        if (count($this->directoryParts) === 0) {
            return new self($this->isAbsolute, $this->aboveBaseLevelCount + 1, $this->drive, $this->directoryParts, null);
        }

        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->drive, array_slice($this->directoryParts, 0, -1), null);
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
     * Returns true if file  path is absolute, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if file path is absolute, false otherwise.
     */
    public function isAbsolute(): bool
    {
        return $this->isAbsolute;
    }

    /**
     * Returns true if file path is a directory, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if file path is a directory, false otherwise.
     */
    public function isDirectory(): bool
    {
        return $this->filename === null;
    }

    /**
     * Returns true if file path is a file, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if file path is a file, false otherwise.
     */
    public function isFile(): bool
    {
        return $this->filename !== null;
    }

    /**
     * Returns true if file path is relative, false otherwise.
     *
     * @since 1.0.0
     *
     * @return bool True if file path is relative, false otherwise.
     */
    public function isRelative(): bool
    {
        return !$this->isAbsolute;
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
     * @throws FilePathInvalidArgumentException if the filename is invalid.
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
        $drive = $filePath->getDrive() ?: $this->getDrive();
        $filename = $filePath->getFilename();

        if ($filePath->isAbsolute()) {
            return new self(true, 0, $drive, $filePath->getDirectoryParts(), $filename);
        }

        $directoryParts = array_merge($this->directoryParts, $filePath->getDirectoryParts());
        $aboveBaseLevel = $this->aboveBaseLevelCount;

        if (!self::normalizeDirectoryParts($this->isAbsolute, $directoryParts, $aboveBaseLevel, $error)) {
            throw new FilePathLogicException('File path "' . $this->__toString() . '" can not be combined with file path "' . $filePath->__toString() . '": ' . $error);
        }

        return new self($this->isAbsolute, $aboveBaseLevel, $drive, $directoryParts, $filename);
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
        $parts = [];

        if ($this->aboveBaseLevelCount > 0) {
            $parts = array_fill(0, $this->aboveBaseLevelCount, '..');
        }

        if ($this->isAbsolute) {
            $parts[] = $this->drive !== null ? $this->drive . ':' : '';
        }

        $parts = array_merge($parts, $this->directoryParts);
        $parts[] = $this->filename ?? '';

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Checks if a file path is valid.
     *
     * @since 1.0.0
     *
     * @param string $string The file path.
     *
     * @return bool True if the file Path parameter is a valid file path, false otherwise.
     */
    public static function isValid(string $string): bool
    {
        return self::doParse($string, false) !== null;
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
     *
     * @param string $string The file path.
     *
     * @throws FilePathInvalidArgumentException If the file path parameter is not a valid file path.
     *
     * @return FilePathInterface The file path instance.
     */
    public static function parse(string $string): FilePathInterface
    {
        $result = self::doParse($string, false, $error);
        if ($result === null) {
            throw new FilePathInvalidArgumentException('File path "' . $string . '" is invalid: ' . $error);
        }

        return $result;
    }

    /**
     * Parses a file path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $string The file path.
     *
     * @throws FilePathInvalidArgumentException If the file path parameter is not a valid file path.
     *
     * @return FilePathInterface The file path instance.
     */
    public static function parseAsDirectory(string $string): FilePathInterface
    {
        $result = self::doParse($string, true, $error);
        if ($result === null) {
            throw new FilePathInvalidArgumentException('File path "' . $string . '" is invalid: ' . $error);
        }

        return $result;
    }

    /**
     * Parses a file path.
     *
     * @since 1.0.0
     *
     * @param string $string The file path.
     *
     * @return FilePathInterface|null The file path instance if the file path parameter is a valid file path, null otherwise.
     */
    public static function tryParse(string $string): ?FilePathInterface
    {
        return self::doParse($string, false);
    }

    /**
     * Parses a file path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $string The file path.
     *
     * @return FilePathInterface|null The file path instance if the file path parameter is a valid file path, null otherwise.
     */
    public static function tryParseAsDirectory(string $string): ?FilePathInterface
    {
        $result = self::doParse($string, true);
        if ($result === null) {
            return null;
        }

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
     * @param string      $str              The file path to parse.
     * @param bool        $parseAsDirectory If true, always parse file path as a directory.
     * @param string|null $error            The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The file path if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, bool $parseAsDirectory, ?string &$error = null): ?self
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
        $directoryParts = [];
        $filename = null;

        if (!self::parseParts($parts, $parseAsDirectory, $isAbsolute, $directoryParts, $filename, $error)) {
            return null;
        }

        $aboveBaseLevel = 0;

        if (!self::normalizeDirectoryParts($isAbsolute, $directoryParts, $aboveBaseLevel, $error)) {
            return null;
        }

        if ($drive !== null && !$isAbsolute) {
            $error = 'Path can not contain drive "' . $drive . '" and non-absolute path "' . $str . '".';

            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $drive, $directoryParts, $filename);
    }

    /**
     * Parses the file path parts.
     *
     * @param string[]    $parts            The parts to parse.
     * @param bool        $parseAsDirectory If true, always parse parts as a directory.
     * @param bool        $isAbsolute       True if parts represents an absolute path, false otherwise.
     * @param array       $directoryParts   The parsed directory parts.
     * @param string|null $filename         The parsed file name.
     * @param string|null $error            The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseParts(array $parts, bool $parseAsDirectory, bool &$isAbsolute, array &$directoryParts, ?string &$filename, ?string &$error): bool
    {
        $isAbsolute = count($parts) > 1 && $parts[0] === '';
        if ($isAbsolute) {
            array_shift($parts);
        }

        $directoryParts = [];
        $filename = null;

        foreach ($parts as $index => $part) {
            $isLast = $index === count($parts) - 1;

            if (!$parseAsDirectory && $isLast && $part !== '.' && $part !== '..') {
                if (!self::validatePart($part, false, $error)) {
                    return false;
                }

                $filename = $part !== '' ? $part : null;

                continue;
            }

            if (!self::validatePart($part, true, $error)) {
                return false;
            }

            $directoryParts[] = $part;
        }

        return true;
    }

    /**
     * Normalizes directory parts, i.e. handles parts that are any of "", ".", "..".
     *
     * @param bool        $isAbsolute     True if parts represents an absolute path, false otherwise.
     * @param string[]    $parts          The directory parts.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string|null $error          The error text if normalizing was not successful, undefined otherwise.
     *
     * @return bool True if normalizing was successful, false otherwise.
     */
    private static function normalizeDirectoryParts(bool $isAbsolute, array &$parts, int &$aboveBaseLevel, ?string &$error = null): bool
    {
        $newParts = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                if (count($newParts) > 0) {
                    array_pop($newParts);

                    continue;
                }

                if ($isAbsolute) {
                    $error = 'Absolute path is above root level.';

                    return false;
                }

                $aboveBaseLevel++;

                continue;
            }

            $newParts[] = $part;
        }

        $parts = $newParts;

        return true;
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
     * Returns true if the operating system is windows, false otherwise.
     *
     * @return bool True if the operating system is windows, false otherwise.
     */
    private static function isWindows(): bool
    {
        return strtolower(substr(php_uname('s'), 0, 7)) === 'windows';
    }

    /**
     * @var string|null The drive or null if no drive is present or supported.
     */
    private ?string $drive;

    /**
     * @var int The number of directory parts above base level.
     */
    private int $aboveBaseLevelCount;

    /**
     * @var string[] The directory parts.
     */
    private array $directoryParts;

    /**
     * @var string|null The filename.
     */
    private ?string $filename;

    /**
     * @var bool True if path is absolute, false otherwise.
     */
    private bool $isAbsolute;
}
