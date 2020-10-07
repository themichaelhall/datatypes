<?php

declare(strict_types=1);

namespace DataTypes\Tests\Helpers\Fakes {

    use function php_uname;

    /**
     * Helper for faking php_uname function.
     */
    class FakePhpUname
    {
        /**
         * Disable fake php_uname.
         */
        public static function disable(): void
        {
            self::$isEnabled = false;
        }

        /**
         * Enable fake php_uname.
         */
        public static function enable(): void
        {
            self::$osName = php_uname('s');
            self::$isEnabled = true;
        }

        /**
         * Returns the operating system name for fake php_uname.
         *
         * @return string The operating system name.
         */
        public static function getOsName(): string
        {
            return self::$osName;
        }

        /**
         * Returns true if fake php_uname is enabled, false otherwise.
         *
         * @return bool True if fake php_uname is enabled, false otherwise.
         */
        public static function isEnabled(): bool
        {
            return self::$isEnabled;
        }

        /**
         * Sets the operating system name to return for fake php_uname.
         *
         * @param string $osName The operating system name.
         */
        public static function setOsName(string $osName): void
        {
            self::$osName = $osName;
        }

        /**
         * @var string|null My operating system name.
         */
        private static $osName = null;

        /**
         * @var bool True if fake php_uname is enabled, false otherwise.
         */
        private static $isEnabled = false;
    }
}

namespace DataTypes {

    use DataTypes\Tests\Helpers\Fakes\FakePhpUname;

    /**
     * Fakes the php_uname method.
     *
     * @param string $mode The mode.
     *
     * @return string The result from either fake or real php_uname.
     */
    function php_uname(string $mode): string
    {
        if (FakePhpUname::isEnabled()) {
            return FakePhpUname::getOsName();
        }

        return \php_uname($mode);
    }
}
