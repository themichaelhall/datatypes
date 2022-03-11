<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Common;

use Exception;

/**
 * Interface for parsable data types.
 *
 * @since 3.0.0
 */
interface ParsableDataTypeInterface extends DataTypeInterface
{
    /**
     * Checks if a string representation of a data type is valid.
     *
     * @since 3.0.0
     *
     * @param string $string The string representation of the data type.
     *
     * @return bool True if the string representation of the data type is valid, false otherwise.
     */
    public static function isValid(string $string): bool;

    /**
     * Parses a string representation of a data type.
     *
     * @since 3.0.0
     *
     * @param string $string The string representation of the data type.
     *
     * @throws Exception If the string representation of the data type is not valid.
     *
     * @return self The parsed data type instance.
     */
    public static function parse(string $string): self;

    /**
     * Parses a string representation of a data type.
     *
     * @since 3.0.0
     *
     * @param string $string The string representation of the data type.
     *
     * @return self|null The parsed data type instance if the string representation of the data type is valid, null otherwise.
     */
    public static function tryParse(string $string): ?self;
}
