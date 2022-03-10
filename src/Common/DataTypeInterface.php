<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Common;

use Stringable;

/**
 * Main interface for all data types.
 *
 * @since 1.0.0
 */
interface DataTypeInterface extends Stringable
{
    /**
     * Returns the data type as a string.
     *
     * @since 1.0.0
     *
     * @return string The data type as a string.
     */
    public function __toString(): string;
}
