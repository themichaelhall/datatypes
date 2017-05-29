<?php

namespace DataTypes\Tests\Issues;

use DataTypes\Url;

/**
 * Tests issue #1 - Url with name and/or password should not be treated as invalid.
 */
class Issue0001Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test isValid method in Url for url with username and password.
     */
    public function testUrlIsValidForUrlWithUsernameAndPassword()
    {
        self::assertTrue(Url::isValid('https://username@domain.com/path/file'));
        self::assertTrue(Url::isValid('https://username:password@domain.com/path/file'));
        self::assertFalse(Url::isValid('https://username:password@foo@domain.com/path/file'));
    }

    /**
     * Test isValidRelative method in Url for url with username and password.
     */
    public function testUrlIsValidRelativeForUrlWithUsernameAndPassword()
    {
        $url = Url::parse('http://localhost/');

        self::assertTrue(Url::isValidRelative('https://username@domain.com/path/file', $url));
        self::assertTrue(Url::isValidRelative('https://username:password@domain.com/path/file', $url));
        self::assertFalse(Url::isValidRelative('https://username:password@foo@domain.com/path/file', $url));
    }

    /**
     * Test tryParse method in Url for url with username and password.
     */
    public function testUrlTryParseForUrlWithUsernameAndPassword()
    {
        self::assertSame('https://domain.com/path/file', Url::tryParse('https://username@domain.com/path/file')->__toString());
        self::assertSame('https://domain.com/path/file', Url::tryParse('https://username:password@domain.com/path/file')->__toString());
        self::assertNull(Url::tryParse('https://username:password@foo@domain.com/path/file'));
    }

    /**
     * Test tryParseRelative method in Url for url with username and password.
     */
    public function testUrlTryParseRelativeForUrlWithUsernameAndPassword()
    {
        $url = Url::parse('http://localhost/');

        self::assertSame('https://domain.com/path/file', Url::tryParseRelative('https://username@domain.com/path/file', $url)->__toString());
        self::assertSame('https://domain.com/path/file', Url::tryParseRelative('https://username:password@domain.com/path/file', $url)->__toString());
        self::assertNull(Url::tryParseRelative('https://username:password@foo@domain.com/path/file', $url));
    }

    /**
     * Test parse method in Url for url with username and password.
     */
    public function testUrlParseForUrlWithUsernameAndPassword()
    {
        self::assertSame('https://domain.com/path/file', Url::parse('https://username@domain.com/path/file')->__toString());
        self::assertSame('https://domain.com/path/file', Url::parse('https://username:password@domain.com/path/file')->__toString());
    }

    /**
     * Test parse method in Url for url with invalid username and password.
     *
     * @expectedException \DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://username:password@foo@domain.com/path/file" is invalid: Host "foo@domain.com" is invalid: Hostname "foo@domain.com" is invalid: Part of domain "foo@domain" contains invalid character "@".
     */
    public function testUrlParseForUrlWithInvalidUsernameAndPassword()
    {
        Url::parse('https://username:password@foo@domain.com/path/file');
    }

    /**
     * Test parseRelative method in Url for url with username and password.
     */
    public function testUrlParseRelativeForUrlWithUsernameAndPassword()
    {
        $url = Url::parse('http://localhost/');

        self::assertSame('https://domain.com/path/file', Url::parseRelative('https://username@domain.com/path/file', $url)->__toString());
        self::assertSame('https://domain.com/path/file', Url::parseRelative('https://username:password@domain.com/path/file', $url)->__toString());
    }

    /**
     * Test parseRelative method in Url for url with invalid username and password.
     *
     * @expectedException \DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://username:password@foo@domain.com/path/file" is invalid: Host "foo@domain.com" is invalid: Hostname "foo@domain.com" is invalid: Part of domain "foo@domain" contains invalid character "@".
     */
    public function testUrlParseRelativeForUrlWithInvalidUsernameAndPassword()
    {
        $url = Url::parse('http://localhost/');

        Url::parseRelative('https://username:password@foo@domain.com/path/file', $url);
    }
}
