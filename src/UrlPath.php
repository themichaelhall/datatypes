<?php

namespace DataTypes;

use DataTypes\Interfaces\UrlPathInterface;

/**
 * Class representing a url path.
 */
class UrlPath implements UrlPathInterface
{
    /**
     * @return string[] The directory parts.
     */
    public function getDirectoryParts()
    {
        return $this->myDirectoryParts;
    }

    /**
     * @return string|null The filename or null if the url path is a directory.
     */
    public function getFilename()
    {
        return $this->myFilename;
    }

    /**
     * @return string The url path as a string.
     */
    public function __toString()
    {
        return '/' . implode('/', $this->myDirectoryParts) . (count($this->myDirectoryParts) > 0 ? '/' : '') . ($this->myFilename !== null ? $this->myFilename : '');
    }

    /**
     * Parses a url path.
     *
     * @param string $urlPath The url path.
     *
     * @return UrlPathInterface The url path instance.
     */
    public static function parse($urlPath)
    {
        assert(is_string($urlPath), '$urlPath is not a string');

        static::myParse($urlPath, $directoryParts, $filename);

        return new self($directoryParts, $filename);
    }

    /**
     * Constructs a url path from value.
     *
     * @param string[]    $directoryParts The directory parts.
     * @param string|null $filename       The filename.
     */
    private function __construct(array $directoryParts, $filename = null)
    {
        $this->myDirectoryParts = $directoryParts;
        $this->myFilename = $filename;
    }

    /**
     * Tries to parse an url path and returns the result or error text.
     *
     * @param string        $urlPath        The url path.
     * @param string[]|null $directoryParts The directory parts if parsing was successful, undefined otherwise.
     * @param string|null   $filename       The file if parsing was not successful, undefined otherwise.
     * @param string|null   $error          The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($urlPath, array &$directoryParts = null, &$filename = null, &$error = null)
    {
        $parts = explode('/', str_replace('\\', '/', $urlPath));
        $partsCount = count($parts);

        $directoryParts = [];
        $filename = null;

        // fixme: Handle relative path
        // fixme: Handle "." part
        // fixme: Handle ".." part
        // fixme: Validate

        // Parse the directories
        for ($i = 0; $i < $partsCount; ++$i) {
            $part = $parts[$i];

            if ($i === 0 && $part === '') {
                continue;
            }

            if ($i === $partsCount - 1) {
                // This is the last part (i.e. the filename part).
                if ($part !== '') {
                    $filename = $part;
                }
            } else {
                // This is a directory part.
                $directoryParts[] = $part;
            }
        }

        return true;
    }

    /**
     * @var string My directory parts.
     */
    private $myDirectoryParts;

    /**
     * @var string|null My filename.
     */
    private $myFilename;
}
