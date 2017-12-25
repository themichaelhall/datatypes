<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
declare(strict_types=1);

namespace DataTypes\Interfaces;

/**
 * Main interface for all data types.
 *
 * @since 1.0.0
 */
interface DataTypeInterface
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
