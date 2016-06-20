<?php
/**
 * Autoload function for DataTypes.
 */
spl_autoload_register(function ($class) {
    list($namespace, $classname) = explode('\\', $class, 2);
    $result = false;

    if ($namespace === 'DataTypes' && in_array($classname,
            [
                'Exceptions\\HostInvalidArgumentException',
                'Exceptions\\HostnameInvalidArgumentException',
                'Exceptions\\IPAddressInvalidArgumentException',
                'Exceptions\\SchemeInvalidArgumentException',
                'Host',
                'Hostname',
                'Interfaces\\DataTypeInterface',
                'Interfaces\\HostInterface',
                'Interfaces\\HostnameInterface',
                'Interfaces\\IPAddressInterface',
                'Interfaces\\SchemeInterface',
                'IPAddress',
                'Scheme',
            ])
    ) {
        /** @noinspection PhpIncludeInspection */
        require __DIR__ . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
        $result = true;
    }

    return $result;
});
