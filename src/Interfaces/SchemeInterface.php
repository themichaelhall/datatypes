<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Scheme class.
 */
interface SchemeInterface extends DataTypeInterface
{
    /**
     * @return int The default port of the scheme.
     */
    public function getDefaultPort();

    /**
     * @return int The type of the scheme.
     */
    public function getType();
}
