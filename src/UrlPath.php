<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

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
     * Returns the directory of the url path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The directory of the url path.
     */
    public function getDirectory()
    {
        return new self($this->myIsAbsolute, $this->myAboveBaseLevel, $this->myDirectoryParts, null);
    }

    /**
     * Returns the parent directory of the url path or null if url path does not have a parent directory.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface|null The parent directory of the url path or null if url path does not have a parent directory.
     */
    public function getParentDirectory()
    {
        if ($this->myParentDirectory($aboveBaseLevel, $directoryParts)) {
            return new self($this->myIsAbsolute, $aboveBaseLevel, $directoryParts, null);
        }

        return null;
    }

    /**
     * Returns The url path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws UrlPathLogicException if the url path could not be made absolute.
     *
     * @return UrlPathInterface The url path as an absolute path.
     */
    public function toAbsolute()
    {
        if ($this->myAboveBaseLevel > 0) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be made absolute: Relative path is above base level.');
        }

        return new self(true, $this->myAboveBaseLevel, $this->myDirectoryParts, $this->myFilename);
    }

    /**
     * Returns the url path as a relative path.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The url path as a relative path.
     */
    public function toRelative()
    {
        return new self(false, $this->myAboveBaseLevel, $this->myDirectoryParts, $this->myFilename);
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
    public function withUrlPath(UrlPathInterface $urlPath)
    {
        if (!$this->myCombine($urlPath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            throw new UrlPathLogicException('Url path "' . $this->__toString() . '" can not be combined with url path "' . $urlPath->__toString() . '": ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Returns the url path as a string.
     *
     * @since 1.0.0
     *
     * @return string The url path as a string.
     */
    public function __toString()
    {
        return $this->myToString('/', function ($s) {
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
     * @throws \InvalidArgumentException If the $urlPath parameter is not a string.
     *
     * @return bool True if the $urlPath parameter is a valid url path, false otherwise.
     */
    public static function isValid($urlPath)
    {
        if (!is_string($urlPath)) {
            throw new \InvalidArgumentException('$urlPath parameter is not a string.');
        }

        return self::myParse(
            '/',
            $urlPath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            });
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     * @throws \InvalidArgumentException       If the $urlPath parameter is not a string.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse($urlPath)
    {
        if (!is_string($urlPath)) {
            throw new \InvalidArgumentException('$urlPath parameter is not a string.');
        }

        if (!self::myParse(
            '/',
            $urlPath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            function ($s) {
                return rawurldecode($s);
            },
            $isAbsolute,
            $aboveBaseLevel,
            $directoryParts,
            $filename,
            $error)
        ) {
            throw new UrlPathInvalidArgumentException($error = 'Url path "' . $urlPath . '" is invalid: ' . $error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Parses a url path.
     *
     * @since 1.0.0
     *
     * @param string $urlPath The url path.
     *
     * @throws \InvalidArgumentException If the $urlPath parameter is not a string.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParse($urlPath)
    {
        if (!is_string($urlPath)) {
            throw new \InvalidArgumentException('$urlPath parameter is not a string.');
        }

        if (!self::myParse(
            '/',
            $urlPath,
            function ($p, $d, &$e) {
                return self::myPartValidator($p, $d, $e);
            },
            function ($s) {
                return rawurldecode($s);
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
     * Constructs a url path from value.
     *
     * @param bool        $isAbsolute     If true url path is absolute, if false url path is relative.
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
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]%-]/', $part, $matches)) {
            $error = ($isDirectory ? 'Part of directory' : 'Filename') . ' "' . $part . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }
}
