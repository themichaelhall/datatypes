<?php

namespace DataTypes;

use DataTypes\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Interfaces\UrlPathInterface;

/**
 * Class representing a url path.
 */
class UrlPath implements UrlPathInterface
{
    /**
     * @return int The depth of the url path.
     */
    public function getDepth()
    {
        return count($this->myDirectoryParts) - $this->myAboveBaseLevel;
    }

    /**
     * @return UrlPath The directory of the url path.
     */
    public function getDirectory()
    {
        return new self($this->myIsAbsolute, $this->myAboveBaseLevel, $this->myDirectoryParts, null);
    }

    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts()
    {
        return $this->myAboveBaseLevel === 0 ? $this->myDirectoryParts : array_merge(array_fill(0, $this->myAboveBaseLevel, '..'), $this->myDirectoryParts);
    }

    /**
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename()
    {
        return $this->myFilename;
    }

    /**
     * @return bool True if url path is absolute, false otherwise.
     */
    public function isAbsolute()
    {
        return $this->myIsAbsolute;
    }

    /**
     * @return bool True if url path is a directory, false otherwise.
     */
    public function isDirectory()
    {
        return $this->myFilename === null;
    }

    /**
     * @return bool True if url path is a file, false otherwise.
     */
    public function isFile()
    {
        return $this->myFilename !== null;
    }

    /**
     * @return bool True if url path is relative, false otherwise.
     */
    public function isRelative()
    {
        return !$this->myIsAbsolute;
    }

    /**
     * @return string The url path as a string.
     */
    public function __toString()
    {
        return
            // If above base level (for relative url path), append the required number of "../".
            ($this->myAboveBaseLevel > 0 ? str_repeat('../', $this->myAboveBaseLevel) : '') .
            // Directory parts.
            ($this->myIsAbsolute ? '/' : '') . implode('/', array_map(function ($directoryPart) {
                return rawurlencode($directoryPart);
            }, $this->myDirectoryParts)) . (count($this->myDirectoryParts) > 0 ? '/' : '') .
            // File part.
            ($this->myFilename !== null ? rawurlencode($this->myFilename) : '');
    }

    /**
     * Checks if a url path is valid.
     *
     * @param string $urlPath The url path.
     *
     * @return bool True if the $urlPath parameter is a valid url path, false otherwise.
     */
    public static function isValid($urlPath)
    {
        assert(is_string($urlPath), '$urlPath is not a string');

        return static::myParse($urlPath);
    }

    /**
     * Parses a url path.
     *
     * @param string $urlPath The url path.
     *
     * @throws UrlPathInvalidArgumentException If the $urlPath parameter is not a valid url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse($urlPath)
    {
        assert(is_string($urlPath), '$urlPath is not a string');

        if (!static::myParse($urlPath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            throw new UrlPathInvalidArgumentException($error);
        }

        return new self($isAbsolute, $aboveBaseLevel, $directoryParts, $filename);
    }

    /**
     * Parses a url path.
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParse($urlPath)
    {
        assert(is_string($urlPath), '$urlPath is not a string');

        if (!static::myParse($urlPath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
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
     * Tries to parse an url path and returns the result or error text.
     *
     * @param string        $urlPath        The url path.
     * @param bool|null     $isAbsolute     Whether the path is absolute or relative is parsing was successful, undefined otherwise.
     * @param int|null      $aboveBaseLevel The number of directory parts above base level if parsing was successful, undefined otherwise.
     * @param string[]|null $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename       The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error          The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($urlPath, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null, &$error = null)
    {
        $parts = explode('/', str_replace('\\', '/', $urlPath));
        $partsCount = count($parts);

        $directoryParts = [];
        $filename = null;
        $isAbsolute = false;
        $aboveBaseLevel = 0;

        // Parse the directories
        for ($i = 0; $i < $partsCount; ++$i) {
            $part = $parts[$i];

            // If the first part is empty and other parts follow, the path begins with "/" and is therefore absolute.
            if ($i === 0 && $part === '' && $partsCount > 1) {
                $isAbsolute = true;

                continue;
            }

            // If part is empty, the path contains "//" and should be skipped.
            if ($part === '') {
                continue;
            }

            // Handle "current directory"-part.
            if ($part === '.') {
                continue;
            }

            // Handle "parent directory"-part.
            if ($part === '..') {
                if (count($directoryParts) === 0) {
                    if ($isAbsolute) {
                        $error = 'Url path "' . $urlPath . '" is invalid: Absolute path is above root level.';

                        return false;
                    }

                    ++$aboveBaseLevel;
                } else {
                    array_pop($directoryParts);
                }

                continue;
            }

            if ($i === $partsCount - 1) {
                // This is the last part (i.e. the filename part).
                if ($part !== '') {
                    if (!static::myValidateFilename($part, $error)) {
                        $error = 'Url path "' . $urlPath . '" is invalid: ' . $error;

                        return false;
                    }

                    $filename = rawurldecode($part);
                }
            } else {
                // This is a directory part.
                if (!static::myValidateDirectoryPart($part, $error)) {
                    $error = 'Url path "' . $urlPath . '" is invalid: ' . $error;

                    return false;
                }

                $directoryParts[] = rawurldecode($part);
            }
        }

        return true;
    }

    /**
     * Validates a directory part.
     *
     * @param string      $directoryPart The directory part.
     * @param string|null $error         The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    public static function myValidateDirectoryPart($directoryPart, &$error = null)
    {
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]%-]/', $directoryPart, $matches)) {
            $error = 'Part of directory "' . $directoryPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates a filename.
     *
     * @param string      $filename The filename.
     * @param string|null $error    The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    public static function myValidateFilename($filename, &$error = null)
    {
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]%-]/', $filename, $matches)) {
            $error = 'Filename "' . $filename . '" contains invalid character "' . $matches[0] . '".';

            return false;
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
     * @var bool True if url path is absolute, false otherwise.
     */
    private $myIsAbsolute;
}
