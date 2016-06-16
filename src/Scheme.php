<?php

namespace DataTypes;

use DataTypes\Interfaces\SchemeInterface;

/**
 * Class representing a scheme.
 */
class Scheme implements SchemeInterface
{
    /**
     * Constructs a scheme.
     *
     * @param string $scheme The scheme.
     */
    public function __construct($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        $this->_value = $scheme;
    }

    /**
     * @return string The scheme as a string.
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
