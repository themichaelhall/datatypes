<?php

use DataTypes\Host;
use DataTypes\Scheme;
use DataTypes\Url;
use DataTypes\UrlPath;

/**
 * Test Url class.
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('http://www.domain.com/', Url::parse('http://www.domain.com/')->__toString());
        $this->assertSame('http://www.domain.com/', Url::parse('http://www.domain.com')->__toString());
        $this->assertSame('http://www.domain.com/foo/Bar', Url::parse('http://www.domain.com/foo/Bar')->__toString());
        $this->assertSame('http://www.domain.com/FOO/BAR', Url::parse('HTTP://WWW.DOMAIN.COM/FOO/BAR')->__toString());
        $this->assertSame('http://www.domain.com:1234/', Url::parse('http://www.domain.com:1234/')->__toString());
        $this->assertSame('http://www.domain.com/foo/Bar?', Url::parse('http://www.domain.com/foo/Bar?')->__toString());
        $this->assertSame('http://www.domain.com/foo/Bar?Baz', Url::parse('http://www.domain.com/foo/Bar?Baz')->__toString());
        $this->assertSame('http://www.domain.com/foo/Bar?F%7Baz%7D', Url::parse('http://www.domain.com/foo/Bar?F%7Baz%7D')->__toString());
    }

    /**
     * Test that empty Url is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "" is empty.
     */
    public function testEmptyUrlIsInvalid()
    {
        Url::parse('');
    }

    /**
     * Test getScheme method.
     */
    public function testGetScheme()
    {
        $this->assertSame(Scheme::TYPE_HTTP, Url::parse('http://foo.bar.com/')->getScheme()->getType());
        $this->assertSame(Scheme::TYPE_HTTPS, Url::parse('https://foo.bar.com/')->getScheme()->getType());
    }

    /**
     * Test that missing scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "foo.bar.com" is invalid: Scheme is missing.
     */
    public function testMissingSchemeIsInvalid()
    {
        Url::parse('foo.bar.com');
    }

    /**
     * Test that empty scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "://foo.bar.com/" is invalid: Scheme "" is empty.
     */
    public function testEmptySchemeIsInvalid()
    {
        Url::parse('://foo.bar.com/');
    }

    /**
     * Test that no scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "//foo.bar.com/" is invalid: Scheme is missing.
     */
    public function testNoSchemeIsInvalid()
    {
        Url::parse('//foo.bar.com/');
    }

    /**
     * Test that invalid scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "baz://foo.bar.com/" is invalid: Scheme "baz" is invalid: Scheme must be "http" or "https"
     */
    public function testInvalidSchemeIsInvalid()
    {
        Url::parse('baz://foo.bar.com/');
    }

    /**
     * Test withScheme method.
     */
    public function testWithScheme()
    {
        $this->assertSame('http://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'))->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'))->__toString());
        $this->assertSame('http://foo.bar.com:443/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'), false)->__toString());
        $this->assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'))->__toString());
        $this->assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'))->__toString());
        $this->assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        $this->assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'), false)->__toString());
    }

    /**
     * Test getHost method.
     */
    public function testGetHost()
    {
        $this->assertSame('foo.bar.com', Url::parse('http://foo.bar.com/path/')->getHost()->__toString());
        $this->assertSame('10.10.10.10', Url::parse('http://10.10.10.10/')->getHost()->__toString());
        $this->assertSame('10.10.10.10', Url::parse('http://10.10.10.10')->getHost()->__toString());
    }

    /**
     * Test that empty host is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://" is invalid: Host "" is empty.
     */
    public function testEmptyHostIsInvalid()
    {
        Url::parse('https://');
    }

    /**
     * Test that invalid host is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://foo@bar" is invalid: Host "foo@bar" is invalid: Hostname "foo@bar" is invalid: Part of domain "foo@bar" contains invalid character "@".
     */
    public function testInvalidHostIsInvalid()
    {
        Url::parse('https://foo@bar');
    }

    /**
     * Test withHost method.
     */
    public function testWithHost()
    {
        $this->assertSame('http://foo.org/path/', Url::parse('http://192.168.0.1/path/')->withHost(Host::parse('foo.org'))->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withHost(Host::parse('foo.bar.com'))->__toString());
    }

    /**
     * Test getPort method.
     */
    public function testGetPort()
    {
        $this->assertSame(80, Url::parse('http://foo.bar.com/path/')->getPort());
        $this->assertSame(443, Url::parse('https://foo.bar.com/path/')->getPort());
        $this->assertSame(1000, Url::parse('https://foo.bar.com:1000/path/')->getPort());
    }

    /**
     * Test that url with invalid character in port is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "http://domain.com:12X45" is invalid: Port "12X45" contains invalid character "X".
     */
    public function testUrlWithInvalidCharacterInPortIsInvalid()
    {
        Url::parse('http://domain.com:12X45');
    }

    /**
     * Test that url with port out of range is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "http://domain.com:65536" is invalid: Port 65536 is out of range: Maximum port number is 65535.
     */
    public function testUrlWithPortOutOfRangeIsInvalid()
    {
        Url::parse('http://domain.com:65536');
    }

    /**
     * Test getPath method.
     */
    public function testGetPath()
    {
        $this->assertSame('/', Url::parse('http://foo.com/')->getPath()->__toString());
        $this->assertSame('/foo/bar', Url::parse('http://domain.com/foo/bar')->getPath()->__toString());
    }

    /**
     * Test that url with invalid path is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://domain.com:1000/foo/{bar}" is invalid: Url path "/foo/{bar}" is invalid: Filename "{bar}" contains invalid character "{".
     */
    public function testUrlWithInvalidPathIsInvalid()
    {
        Url::parse('https://domain.com:1000/foo/{bar}');
    }

    /**
     * Test parse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $url parameter is not a string.
     */
    public function testParseWithInvalidArgumentType()
    {
        Url::parse(true);
    }

    /**
     * Test getQueryString method.
     */
    public function testGetQueryString()
    {
        $this->assertNull(Url::parse('http://domain.com/foo')->getQueryString());
        $this->assertSame('', Url::parse('http://domain.com/foo?')->getQueryString());
        $this->assertSame('bar=baz', Url::parse('http://domain.com/foo?bar=baz')->getQueryString());
        $this->assertSame('F%7Baz%7D', Url::parse('http://domain.com/foo?F%7Baz%7D')->getQueryString());
    }

    /**
     * Test fromParts method.
     */
    public function testFromParts()
    {
        $this->assertSame('https://www.domain.com:1000/foo/bar?query', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), 1000, UrlPath::parse('/foo/bar'), 'query')->__toString());
        $this->assertSame('https://www.domain.com/foo/bar?query', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'), 'query')->__toString());
        $this->assertSame('https://www.domain.com/foo/bar?', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'), '')->__toString());
        $this->assertSame('https://www.domain.com/foo/bar', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'))->__toString());
    }

    /**
     * Test that using fromParts method with port number below 0 is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Port -1 is out of range: Minimum port number is 0.
     */
    public function testFromPartsWithPortNumberBelow0IsInvalid()
    {
        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), -1, UrlPath::parse('/'));
    }

    /**
     * Test that using fromParts method with port number above 65535 is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Port 65536 is out of range: Maximum port number is 65535.
     */
    public function testFromPartsWithPortNumberAbove65535IsInvalid()
    {
        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), 65536, UrlPath::parse('/'));
    }

    /**
     * Test that using fromParts method with relative url path is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url path "foo/" is relative.
     */
    public function testFromPartsWithRelativeUrlPathIsInvalid()
    {
        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), null, UrlPath::parse('foo/'));
    }

    /**
     * Test fromParts method with invalid port argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $port parameter is not an integer or null.
     */
    public function testFromPartsWithInvalidPortArgumentType()
    {
        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), '80', UrlPath::parse('/'));
    }

    /**
     * Test fromParts method with invalid query string argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $queryString parameter is not a string or null.
     */
    public function testFromPartsWithInvalidQueryStringArgumentType()
    {
        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), null, UrlPath::parse('/'), ['foo']);
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Url::isValid(''));
        $this->assertFalse(Url::isValid('foo://bar.com/'));
        $this->assertTrue(Url::isValid('http://domain.com/'));
        $this->assertFalse(Url::isValid('http:///path/'));
        $this->assertFalse(Url::isValid('http://+++/'));
        $this->assertFalse(Url::isValid('http://domain.com:XXX/'));
        $this->assertTrue(Url::isValid('http://domain.com:1234/'));
        $this->assertFalse(Url::isValid('http://domain.com:1234/{foo}'));
        $this->assertTrue(Url::isValid('http://domain.com/foo?bar'));
        // fixme: More tests
    }

    /**
     * Test isValid method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $url parameter is not a string.
     */
    public function testIsValidWithInvalidArgumentType()
    {
        Url::isValid([]);
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Url::tryParse(''));
        $this->assertNull(Url::tryParse('foo://bar.com/'));
        $this->assertSame('http://domain.com/', Url::tryParse('http://domain.com/')->__toString());
        $this->assertNull(Url::tryParse('http:///path/'));
        $this->assertNull(Url::tryParse('http://+++/'));
        $this->assertNull(Url::tryParse('http://domain.com:XXX/'));
        $this->assertSame('http://domain.com:1234/', Url::tryParse('http://domain.com:1234/')->__toString());
        $this->assertNull(Url::tryParse('http://domain.com:1234/{foo}'));
        $this->assertSame('http://domain.com/foo?bar', Url::tryParse('http://domain.com/foo?bar')->__toString());
        // fixme: More tests
    }

    /**
     * Test parseRelative method.
     */
    public function testParseRelative()
    {
        $url = Url::parse('http://foo.com:8080/path/file?query');

        $this->assertSame('https://bar.com/new-path/new-file?new-query', Url::parseRelative('https://bar.com/new-path/new-file?new-query', $url)->__toString());
        $this->assertSame('http://bar.com/new-path/new-file?new-query', Url::parseRelative('//bar.com/new-path/new-file?new-query', $url)->__toString());
        $this->assertSame('http://foo.com:8080/new-path/new-file?new-query', Url::parseRelative('/new-path/new-file?new-query', $url)->__toString());
        $this->assertSame('http://foo.com:8080/path/new-file?new-query', Url::parseRelative('new-file?new-query', $url)->__toString());
        $this->assertSame('http://foo.com:8080/new-file?new-query', Url::parseRelative('../new-file?new-query', $url)->__toString());
        // fixme: Query string
        // fixme: Empty url
    }

    /**
     * Test parseRelative method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $url parameter is not a string.
     */
    public function testParseRelativeWithInvalidArgumentType()
    {
        Url::parseRelative(null, Url::parse('http://domain.com/'));
    }

    /**
     * Test parseRelative method with empty scheme.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "://domain.com/" is invalid: Scheme "" is empty.
     */
    public function testParseRelativeWithEmptyScheme()
    {
        Url::parseRelative('://domain.com/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid scheme.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "baz://domain.com/" is invalid: Scheme "baz" is invalid: Scheme must be "http" or "https".
     */
    public function testParseRelativeWithInvalidScheme()
    {
        Url::parseRelative('baz://domain.com/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    // fixme: Test parse relative with invalid host
    // fixme: Test parse relative with invalid port
    // fixme: Test parse relative with invalid path

    /**
     * Test parse relative path with path that resolves above root level.
     *
     * @expectedException \DataTypes\Exceptions\UrlPathLogicException
     * @expectedExceptionMessage Url path "/foo/" can not be combined with url path "../../bar": Absolute path is above root level.
     */
    public function testParseRelativePathWithPathAboveRootLevel()
    {
        echo Url::parseRelative('../../bar', Url::parse('http://localhost/foo/'));
    }

    /**
     * Test tryParse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $url parameter is not a string.
     */
    public function testTryParseWithInvalidArgumentType()
    {
        Url::tryParse(67890);
    }
}
