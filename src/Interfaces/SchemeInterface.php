<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Scheme class.
 */
interface SchemeInterface extends DataTypeInterface
{
    /**
     * @return int The type of the scheme.
     */
    public function getType();
}
