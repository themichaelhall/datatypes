<?php

declare(strict_types=1);

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace {

    /**
     * Helper for faking php_uname function.
     */
    class FakePhpUname
    {
        /**
         * Disable fake php_uname.
         */
        public static function disable()
        {
            self::$isEnabled = false;
        }

        /**
         * Enable fake php_uname.
         */
        public static function enable()
        {
            self::$osName = 'Other';
            self::$isEnabled = true;
        }

        /**
         * Returns the operating system name for fake php_uname.
         *
         * @return string The operating system name.
         */
        public static function getOsName()
        {
            return self::$osName;
        }

        /**
         * Returns true if fake php_uname is enabled, false otherwise.
         *
         * @return bool True if fake php_uname is enabled, false otherwise.
         */
        public static function isEnabled()
        {
            return self::$isEnabled;
        }

        /**
         * Sets the operating system name to return for fake php_uname.
         *
         * @param string $osName The operating system name.
         */
        public static function setOsName($osName)
        {
            self::$osName = $osName;
        }

        /**
         * @var string My operating system name.
         */
        private static $osName = 'Other';

        /**
         * @var bool True if fake php_uname is enabled, false otherwise.
         */
        private static $isEnabled = false;
    }
}

namespace DataTypes {

    use FakePhpUname;

    /**
     * Fakes the php_uname method.
     *
     * @param string $mode The mode.
     *
     * @return string The result from either fake or real php_uname.
     */
    function php_uname($mode)
    {
        if (FakePhpUname::isEnabled()) {
            return FakePhpUname::getOsName();
        }

        return \php_uname($mode);
    }
}
