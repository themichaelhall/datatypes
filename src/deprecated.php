<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

/** @noinspection PhpIgnoredClassAliasDeclaration */
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

declare(strict_types=1);

namespace {

    class_alias(DataTypes\Core\DataTypeInterface::class, 'DataTypes\\Interfaces\\DataTypeInterface');
    class_alias(DataTypes\Core\PathInterface::class, 'DataTypes\\Interfaces\\PathInterface');
    class_alias(DataTypes\Net\EmailAddress::class, 'DataTypes\\EmailAddress');
    class_alias(DataTypes\Net\EmailAddressInterface::class, 'DataTypes\\Interfaces\\EmailAddressInterface');
    class_alias(DataTypes\Net\Host::class, 'DataTypes\\Host');
    class_alias(DataTypes\Net\HostInterface::class, 'DataTypes\\Interfaces\\HostInterface');
    class_alias(DataTypes\Net\Hostname::class, 'DataTypes\\Hostname');
    class_alias(DataTypes\Net\HostnameInterface::class, 'DataTypes\\Interfaces\\HostnameInterface');
    class_alias(DataTypes\Net\IPAddress::class, 'DataTypes\\IPAddress');
    class_alias(DataTypes\Net\IPAddressInterface::class, 'DataTypes\\Interfaces\\IPAddressInterface');
    class_alias(DataTypes\Net\Scheme::class, 'DataTypes\\Scheme');
    class_alias(DataTypes\Net\SchemeInterface::class, 'DataTypes\\Interfaces\\SchemeInterface');
    class_alias(DataTypes\Net\Url::class, 'DataTypes\\Url');
    class_alias(DataTypes\Net\UrlInterface::class, 'DataTypes\\Interfaces\\UrlInterface');
    class_alias(DataTypes\Net\UrlPath::class, 'DataTypes\\UrlPath');
    class_alias(DataTypes\Net\UrlPathInterface::class, 'DataTypes\\Interfaces\\UrlPathInterface');
    class_alias(DataTypes\System\FilePath::class, 'DataTypes\\FilePath');
    class_alias(DataTypes\System\FilePathInterface::class, 'DataTypes\\Interfaces\\FilePathInterface');
}

// The code below is never executed. It's purpose is to issue a deprecated-warning for IDEs supporting this functionality.

namespace DataTypes {

    if (false) {
        /** @deprecated Use DataTypes\Net\EmailAddress instead. */
        class EmailAddress extends \DataTypes\Net\EmailAddress
        {
        }

        /** @deprecated Use DataTypes\Net\Host instead. */
        class Host extends \DataTypes\Net\Host
        {
        }

        /** @deprecated Use DataTypes\Net\Hostname instead. */
        class Hostname extends \DataTypes\Net\Hostname
        {
        }

        /** @deprecated Use DataTypes\Net\IPAddress instead. */
        class IPAddress extends \DataTypes\Net\IPAddress
        {
        }

        /** @deprecated Use DataTypes\Net\Scheme instead. */
        class Scheme extends \DataTypes\Net\Scheme
        {
        }

        /** @deprecated Use DataTypes\Net\Url instead. */
        class Url extends \DataTypes\Net\Url
        {
        }

        /** @deprecated Use DataTypes\Net\UrlPath instead. */
        class UrlPath extends \DataTypes\Net\UrlPath
        {
        }

        /** @deprecated Use DataTypes\System\FilePath instead. */
        class FilePath extends \DataTypes\System\FilePath
        {
        }
    }
}

namespace DataTypes\Interfaces {

    if (false) {
        /** @deprecated Use DataTypes\Core\DataTypeInterface instead. */
        interface DataTypeInterface extends \DataTypes\Core\DataTypeInterface
        {
        }

        /** @deprecated Use DataTypes\Core\PathInterface instead. */
        interface PathInterface extends \DataTypes\Core\PathInterface
        {
        }

        /** @deprecated Use DataTypes\Net\EmailAddressInterface instead. */
        interface EmailAddressInterface extends \DataTypes\Net\EmailAddressInterface
        {
        }

        /** @deprecated Use DataTypes\Net\HostInterface instead. */
        interface HostInterface extends \DataTypes\Net\HostInterface
        {
        }

        /** @deprecated Use DataTypes\Net\HostnameInterface instead. */
        interface HostnameInterface extends \DataTypes\Net\HostnameInterface
        {
        }

        /** @deprecated Use DataTypes\Net\IPAddressInterface instead. */
        interface IPAddressInterface extends \DataTypes\Net\IPAddressInterface
        {
        }

        /** @deprecated Use DataTypes\Net\SchemeInterface instead. */
        interface SchemeInterface extends \DataTypes\Net\SchemeInterface
        {
        }

        /** @deprecated Use DataTypes\Net\UrlInterface instead. */
        interface UrlInterface extends \DataTypes\Net\UrlInterface
        {
        }

        /** @deprecated Use DataTypes\Net\UrlPathInterface instead. */
        interface UrlPathInterface extends \DataTypes\Net\UrlPathInterface
        {
        }

        /** @deprecated Use DataTypes\Net\FilePathInterface instead. */
        interface FilePathInterface extends \DataTypes\System\FilePathInterface
        {
        }
    }
}
