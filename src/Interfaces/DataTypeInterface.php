<?php

namespace DataTypes\Interfaces;

/**
 * Main interface for all data types.
 */
interface DataTypeInterface
{
    /**
     * @return string The data type as a string.
     */
    public function __toString();
}
