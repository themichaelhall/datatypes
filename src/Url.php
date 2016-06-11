<?php

namespace DataTypes;

use DataTypes\Interfaces\UrlInterface;

/**
 * Class representing a Url.
 */
class Url implements UrlInterface
{
    /**
     * Constructs a Url.
     *
     * @param string $url The Url.
     */
    public function __construct($url)
    {
        assert(is_string($url), '$url is not a string');

        // fixme: Validation
        // fixme: Scheme
        // fixme: User
        // fixme: Password
        // fixme: Host
        // fixme: Port
        // fixme: Path
        // fixme: Query
        // fixme: Fragment

        $this->_value = $url;
    }

    /**
     * @return string The Url as a string.
     */
    public function __toString()
    {
        return $this->_value;
    }

    /**
     * @var string My value.
     */
    private $_value;
}
