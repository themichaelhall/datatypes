<?php

namespace DataTypes;

use DataTypes\Interfaces\UrlPathInterface;

/**
 * Class representing a url path.
 */
class UrlPath implements UrlPathInterface
{
    /**
     * @return string The url path as a string.
     */
    public function __toString()
    {
        return $this->myValue;
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

        return new self($urlPath);
    }

    /**
     * Constructs a url path from value.
     *
     * @param string $value The value.
     */
    private function __construct($value)
    {
        $this->myValue = $value;
    }

    /**
     * @var string My value.
     */
    private $myValue;
}
