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

    class_alias(DataTypes\Common\DataTypeInterface::class, 'DataTypes\\Interfaces\\DataTypeInterface');
    class_alias(DataTypes\Common\PathInterface::class, 'DataTypes\\Interfaces\\PathInterface');
    class_alias(DataTypes\Net\EmailAddress::class, 'DataTypes\\EmailAddress');
    class_alias(DataTypes\Net\EmailAddressInterface::class, 'DataTypes\\Interfaces\\EmailAddressInterface');
    class_alias(DataTypes\Net\Exceptions\EmailAddressInvalidArgumentException::class, 'DataTypes\\Exceptions\\EmailAddressInvalidArgumentException');
    class_alias(DataTypes\Net\Host::class, 'DataTypes\\Host');
    class_alias(DataTypes\Net\HostInterface::class, 'DataTypes\\Interfaces\\HostInterface');
    class_alias(DataTypes\Net\Exceptions\HostInvalidArgumentException::class, 'DataTypes\\Exceptions\\HostInvalidArgumentException');
    class_alias(DataTypes\Net\Hostname::class, 'DataTypes\\Hostname');
    class_alias(DataTypes\Net\HostnameInterface::class, 'DataTypes\\Interfaces\\HostnameInterface');
    class_alias(DataTypes\Net\Exceptions\HostnameInvalidArgumentException::class, 'DataTypes\\Exceptions\\HostnameInvalidArgumentException');
    class_alias(DataTypes\Net\IPAddress::class, 'DataTypes\\IPAddress');
    class_alias(DataTypes\Net\IPAddressInterface::class, 'DataTypes\\Interfaces\\IPAddressInterface');
    class_alias(DataTypes\Net\Exceptions\IPAddressInvalidArgumentException::class, 'DataTypes\\Exceptions\\IPAddressInvalidArgumentException');
    class_alias(DataTypes\Net\Scheme::class, 'DataTypes\\Scheme');
    class_alias(DataTypes\Net\SchemeInterface::class, 'DataTypes\\Interfaces\\SchemeInterface');
    class_alias(DataTypes\Net\Exceptions\SchemeInvalidArgumentException::class, 'DataTypes\\Exceptions\\SchemeInvalidArgumentException');
    class_alias(DataTypes\Net\Url::class, 'DataTypes\\Url');
    class_alias(DataTypes\Net\UrlInterface::class, 'DataTypes\\Interfaces\\UrlInterface');
    class_alias(DataTypes\Net\Exceptions\UrlInvalidArgumentException::class, 'DataTypes\\Exceptions\\UrlInvalidArgumentException');
    class_alias(DataTypes\Net\UrlPath::class, 'DataTypes\\UrlPath');
    class_alias(DataTypes\Net\UrlPathInterface::class, 'DataTypes\\Interfaces\\UrlPathInterface');
    class_alias(DataTypes\Net\Exceptions\UrlPathInvalidArgumentException::class, 'DataTypes\\Exceptions\\UrlPathInvalidArgumentException');
    class_alias(DataTypes\Net\Exceptions\UrlPathLogicException::class, 'DataTypes\\Exceptions\\UrlPathLogicException');
    class_alias(DataTypes\System\FilePath::class, 'DataTypes\\FilePath');
    class_alias(DataTypes\System\FilePathInterface::class, 'DataTypes\\Interfaces\\FilePathInterface');
    class_alias(DataTypes\System\Exceptions\FilePathInvalidArgumentException::class, 'DataTypes\\Exceptions\\FilePathInvalidArgumentException');
    class_alias(DataTypes\System\Exceptions\FilePathLogicException::class, 'DataTypes\\Exceptions\\FilePathLogicException');
}

// NOTE:
// The code below is never executed. Its purpose is to issue a deprecated-warning for IDEs supporting this functionality.
// If other warnings should appear from this, fix the deprecation warnings first and try again.
// Code using the deprecated classes should still be running without issues.

namespace DataTypes {

    use DataTypes\Interfaces\EmailAddressInterface;
    use DataTypes\Interfaces\FilePathInterface;
    use DataTypes\Interfaces\HostInterface;
    use DataTypes\Interfaces\HostnameInterface;
    use DataTypes\Interfaces\IPAddressInterface;
    use DataTypes\Interfaces\SchemeInterface;
    use DataTypes\Interfaces\UrlInterface;
    use DataTypes\Interfaces\UrlPathInterface;

    if (false) {
        /** @deprecated Use \DataTypes\Net\EmailAddress instead. */
        class EmailAddress extends \DataTypes\Net\EmailAddress implements EmailAddressInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\Host instead. */
        class Host extends \DataTypes\Net\Host implements HostInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\Hostname instead. */
        class Hostname extends \DataTypes\Net\Hostname implements HostnameInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\IPAddress instead. */
        class IPAddress extends \DataTypes\Net\IPAddress implements IPAddressInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\Scheme instead. */
        class Scheme extends \DataTypes\Net\Scheme implements SchemeInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\Url instead. */
        class Url extends \DataTypes\Net\Url implements UrlInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\UrlPath instead. */
        class UrlPath extends \DataTypes\Net\UrlPath implements UrlPathInterface
        {
        }

        /** @deprecated Use \DataTypes\System\FilePath instead. */
        class FilePath extends \DataTypes\System\FilePath implements FilePathInterface
        {
        }
    }
}

namespace DataTypes\Interfaces {

    if (false) {
        /** @deprecated Use \DataTypes\Common\DataTypeInterface instead. */
        interface DataTypeInterface extends \DataTypes\Common\DataTypeInterface
        {
        }

        /** @deprecated Use \DataTypes\Common\PathInterface instead. */
        interface PathInterface extends \DataTypes\Common\PathInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\EmailAddressInterface instead. */
        interface EmailAddressInterface extends \DataTypes\Net\EmailAddressInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\HostInterface instead. */
        interface HostInterface extends \DataTypes\Net\HostInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\HostnameInterface instead. */
        interface HostnameInterface extends \DataTypes\Net\HostnameInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\IPAddressInterface instead. */
        interface IPAddressInterface extends \DataTypes\Net\IPAddressInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\SchemeInterface instead. */
        interface SchemeInterface extends \DataTypes\Net\SchemeInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\UrlInterface instead. */
        interface UrlInterface extends \DataTypes\Net\UrlInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\UrlPathInterface instead. */
        interface UrlPathInterface extends \DataTypes\Net\UrlPathInterface
        {
        }

        /** @deprecated Use \DataTypes\Net\FilePathInterface instead. */
        interface FilePathInterface extends \DataTypes\System\FilePathInterface
        {
        }
    }
}

namespace DataTypes\Exceptions {

    if (false) {
        /** @deprecated Use \DataTypes\Net\Exceptions\EmailAddressInvalidArgumentException instead. */
        class EmailAddressInvalidArgumentException extends \DataTypes\Net\Exceptions\EmailAddressInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\HostInvalidArgumentException instead. */
        class HostInvalidArgumentException extends \DataTypes\Net\Exceptions\HostInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\HostnameInvalidArgumentException instead. */
        class HostnameInvalidArgumentException extends \DataTypes\Net\Exceptions\HostnameInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\IPAddressInvalidArgumentException instead. */
        class IPAddressInvalidArgumentException extends \DataTypes\Net\Exceptions\IPAddressInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\SchemeInvalidArgumentException instead. */
        class SchemeInvalidArgumentException extends \DataTypes\Net\Exceptions\SchemeInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\UrlInvalidArgumentException instead. */
        class UrlInvalidArgumentException extends \DataTypes\Net\Exceptions\UrlInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\UrlPathInvalidArgumentException instead. */
        class UrlPathInvalidArgumentException extends \DataTypes\Net\Exceptions\UrlPathInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\Net\Exceptions\UrlPathLogicException instead. */
        class UrlPathLogicException extends \DataTypes\Net\Exceptions\UrlPathLogicException
        {
        }

        /** @deprecated Use \DataTypes\System\Exceptions\FilePathInvalidArgumentException instead. */
        class FilePathInvalidArgumentException extends \DataTypes\System\Exceptions\FilePathInvalidArgumentException
        {
        }

        /** @deprecated Use \DataTypes\System\Exceptions\FilePathLogicException instead. */
        class FilePathLogicException extends \DataTypes\System\Exceptions\FilePathLogicException
        {
        }
    }
}
