<?php
/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */
declare(strict_types=1);

namespace DataTypes;

use DataTypes\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Exceptions\UrlPathLogicException;
use DataTypes\Interfaces\UrlPathInterface;
use DataTypes\Traits\PathTrait;

/**
 * Class representing a url path.
 *
 * @since 1.0.0
 */
class UrlPath implements UrlPathInterface
{
    use PathTrait;

    /**
     * Returns true if the url path equals other url path, false otherwise.
     *
     * @since 1.2.0
     *
     * @param UrlPathInterface $urlPath The other url path.
     *
     * @return bool True if the url path equals other url path, false otherwise.
     */
    public function equals(UrlPathInterface $urlPath): bool
    {
        return $this->isAbsolute() === $urlPath->isAbsolute() && $this->getDirectoryParts() === $urlPath->getDirectoryParts() && $this->getFilename() === $urlPath->getFilename();
    }

    /**
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory(): UrlPathInterface
    {
        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->directoryParts, null);
    }

    /**
     * Returns the parent directory of the url path or null if url path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface|null The parent directory of the url path or null if url path does not have a parent directory.
     */
    public function getParentDirectory(): ?UrlPathInterface
    {
        if ($this->calculateParentDirectory($aboveBaseLevelCount, $directoryParts)) {
            return new self($this->isAbsolute, $aboveBaseLevelCount, $directoryParts, null);
        }

        return null;
    }

    /**
     * Returns a copy of the url path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws UrlPathLogicException if the url path could not be made absolute.
     *
     * @return UrlPathInterface The url path as an absolute path.
     */
    public function toAbsolute(): UrlPathInterface
    {
        if ($this->aboveBaseLevelCount > 0) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->aboveBaseLevelCount, $this->directoryParts, $this->filename);
    }

    /**
     * Returns a copy of the url path as a relative path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative(): UrlPathInterface
    {
        return new self(false, $this->aboveBaseLevelCount, $this->directoryParts, $this->filename);
    }

    /**
     * Returns a copy of the url path combined with another url path.
     *
     * @since 1.0.0
     *
     * @param UrlPathInterface $urlPath The other url path.
     *
     * @throws UrlPathLogicException if the url paths could not be combined.
     *
     * @return UrlPathInterface The combined url path.
     */
    public function withUrlPath(UrlPathInterface $urlPath): UrlPathInterface
    {
        $result = new self(
            $this->isAbsolute,
            $this->aboveBaseLevelCount,
            $this->directoryParts,
            $urlPath->getFilename()
        );

        if (!$result->combineDirectory($urlPath->isAbsolute(), $urlPath->getDirectoryParts(), $error)) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be combined with url path "' . $urlPath->__toString() . '": ' . $error);
        }

        return $result;
    }

    /**
     * Returns a copy of the url path with another filename.
     *
     * @since 2.2.0
     *
     * @param string $filename The other filename
     *
     * @throws UrlPathInvalidArgumentException if the filename if invalid.
     *
     * @return UrlPathInterface The new url path.
     */
    public function withFilename(string $filename): UrlPathInterface
    {
        if (!self::validatePart($filename, false, $error)) {
            throw new UrlPathInvalidArgumentException($error);
        }

        return new self($this->isAbsolute, $this->aboveBaseLevelCount, $this->directoryParts, $filename);
    }

    /**
     * Returns the url path as a string.
     *
     * @since 1.0.0
     *
     * @return string The url path as a string.
     */
    public function __toString(): string
    {
        return $this->toString(self::DIRECTORY_SEPARATOR, function ($s) {
            return rawurlencode($s);
        });
    }

    /**
     * Checks if a url path is valid.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @return bool True if the $path parameter is a valid url path, false otherwise.
     */
    public static function isValid(string $urlPath): bool
    {
        return self::doParse($urlPath) !== null;
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse(string $urlPath): UrlPathInterface
    {
        $result = self::doParse($urlPath, $error);
        if ($result === null) {
            throw new UrlPathInvalidArgumentException('Url path "' . $urlPath . '" is invalid: ' . $error);
        }

        return $result;
    }

    /**
     * Parses a url path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parseAsDirectory(string $urlPath): UrlPathInterface
    {
        $result = self::doParse($urlPath, $error);
        if ($result === null) {
            throw new UrlPathInvalidArgumentException('Url path "' . $urlPath . '" is invalid: ' . $error);
        }

        $result->convertToDirectory();

        return $result;
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParse(string $urlPath): ?UrlPathInterface
    {
        return self::doParse($urlPath);
    }

    /**
     * Parses a url path as a directory, regardless if the input ends with a directory separator or not.
     *
     * @since 2.2.0
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParseAsDirectory(string $urlPath): ?UrlPathInterface
    {
        $result = self::doParse($urlPath);
        if ($result === null) {
            return null;
        }

        $result->convertToDirectory();

        return $result;
    }

    /**
     * Constructs a url path from value.
     *
     * @param bool        $isAbsolute     If true url path is absolute, if false url path is relative.
     * @param int         $aboveBaseLevel The number of directory parts above base level.
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    private function __construct(bool $isAbsolute, int $aboveBaseLevel, array $directoryParts, ?string $filename)
    {
        $this->isAbsolute = $isAbsolute;
        $this->aboveBaseLevelCount = $aboveBaseLevel;
        $this->directoryParts = $directoryParts;
        $this->filename = $filename;
    }

    /**
     * Tries to parse a url path and returns the result or null.
     *
     * @param string      $str   The url path to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The url path if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        $parts = explode(self::DIRECTORY_SEPARATOR, $str);

        $isAbsolute = false;
        $aboveBaseLevel = 0;
        $directoryParts = [];
        $filename = null;

        if (!self::parseParts($parts, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            return null;
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
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
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*+,;=:@\[\]%-]/', $part, $matches)) {
            $error = ($isDirectory ? 'Part of directory' : 'Filename') . ' "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Decodes a directory part or a file name.
     *
     * @param string $part The directory part or a file name.
     *
     * @return string The decoded directory part or file name.
     */
    private static function decodePart(string $part): string
    {
        return rawurldecode($part);
    }

    /**
     * @var string My directory separator.
     */
    private const DIRECTORY_SEPARATOR = '/';
}
