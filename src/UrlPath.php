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
     * @return UrlPath The directory of the url path.
     */
    public function getDirectory()
    {
        return new self($this->myIsAbsolute, $this->myAboveBaseLevel, $this->myDirectoryParts, null);
    }

    /**
     * Returns The url path as an absolute path.
     *
     * @since 1.0.0
     *
     * @throws UrlPathLogicException if the url path could not be made absolute.
     *
     * @return UrlPath The url path as an absolute path.
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
     * @return UrlPath The url path as a relative path.
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
     * @return UrlPath The combined url path.
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
     * @return bool True if the $urlPath parameter is a valid url path, false otherwise.
     */
    public static function isValid($urlPath)
    {
        assert(is_string($urlPath), '$urlPath is not a string');

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
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse($urlPath)
    {
        // fixme: use myParse from PathTrait
        assert(is_string($urlPath), '$urlPath is not a string');

        if (!static::myParse2($urlPath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
            throw new UrlPathInvalidArgumentException($error);
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
     * @return UrlPathInterface|null The url path instance if the $urlPath parameter is a valid url path, null otherwise.
     */
    public static function tryParse($urlPath)
    {
        // fixme: use myParse from PathTrait
        assert(is_string($urlPath), '$urlPath is not a string');

        if (!static::myParse2($urlPath, $isAbsolute, $aboveBaseLevel, $directoryParts, $filename, $error)) {
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
    private static function myParse2($urlPath, &$isAbsolute = null, &$aboveBaseLevel = null, array &$directoryParts = null, &$filename = null, &$error = null)
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
    private static function myValidateDirectoryPart($directoryPart, &$error = null)
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
    private static function myValidateFilename($filename, &$error = null)
    {
        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]%-]/', $filename, $matches)) {
            $error = 'Filename "' . $filename . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
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
            return false;
        }

        return true;
    }
}
