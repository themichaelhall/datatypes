<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net;

use DataTypes\Net\Exceptions\UrlInvalidArgumentException;
use DataTypes\Net\Exceptions\UrlPathLogicException;
use DataTypes\Net\Host;
use DataTypes\Net\Scheme;
use DataTypes\Net\Url;
use DataTypes\Net\UrlPath;
use PHPUnit\Framework\TestCase;

/**
 * Test Url class.
 */
class UrlTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        self::assertSame('http://www.domain.com/', Url::parse('http://www.domain.com/')->__toString());
        self::assertSame('http://www.domain.com/', Url::parse('http://www.domain.com')->__toString());
        self::assertSame('http://www.domain.com/foo/Bar', Url::parse('http://www.domain.com/foo/Bar')->__toString());
        self::assertSame('http://www.domain.com/FOO/BAR', Url::parse('HTTP://WWW.DOMAIN.COM/FOO/BAR')->__toString());
        self::assertSame('http://www.domain.com:1234/', Url::parse('http://www.domain.com:1234/')->__toString());
        self::assertSame('http://www.domain.com/foo/Bar?', Url::parse('http://www.domain.com/foo/Bar?')->__toString());
        self::assertSame('http://www.domain.com/foo/Bar?Baz', Url::parse('http://www.domain.com/foo/Bar?Baz')->__toString());
        self::assertSame('http://www.domain.com/foo/Bar?F%7Baz%7D', Url::parse('http://www.domain.com/foo/Bar?F%7Baz%7D')->__toString());
        self::assertSame('http://www.domain.com/?foo', Url::parse('http://www.domain.com/?foo')->__toString());
        self::assertSame('http://www.domain.com/#bar', Url::parse('http://www.domain.com/#bar')->__toString());
        self::assertSame('http://www.domain.com/?foo#bar', Url::parse('http://www.domain.com/?foo#bar')->__toString());
        self::assertSame('http://www.domain.com/path/file?foo#bar', Url::parse('http://www.domain.com/path/file?foo#bar')->__toString());
    }

    /**
     * Test that empty Url is invalid.
     */
    public function testEmptyUrlIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "" is empty.');

        Url::parse('');
    }

    /**
     * Test getScheme method.
     */
    public function testGetScheme()
    {
        self::assertSame(Scheme::TYPE_HTTP, Url::parse('http://foo.bar.com/')->getScheme()->getType());
        self::assertSame(Scheme::TYPE_HTTPS, Url::parse('https://foo.bar.com/')->getScheme()->getType());
    }

    /**
     * Test that missing scheme is invalid.
     */
    public function testMissingSchemeIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "foo.bar.com" is invalid: Scheme is missing.');

        Url::parse('foo.bar.com');
    }

    /**
     * Test that empty scheme is invalid.
     */
    public function testEmptySchemeIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "://foo.bar.com/" is invalid: Scheme "" is empty.');

        Url::parse('://foo.bar.com/');
    }

    /**
     * Test that no scheme is invalid.
     */
    public function testNoSchemeIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "//foo.bar.com/" is invalid: Scheme is missing.');

        Url::parse('//foo.bar.com/');
    }

    /**
     * Test that invalid scheme is invalid.
     */
    public function testInvalidSchemeIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "baz://foo.bar.com/" is invalid: Scheme "baz" is invalid: Scheme must be "http" or "https".');

        Url::parse('baz://foo.bar.com/');
    }

    /**
     * Test withScheme method.
     */
    public function testWithScheme()
    {
        self::assertSame('http://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'))->__toString());
        self::assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'))->__toString());
        self::assertSame('http://foo.bar.com:443/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        self::assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'), false)->__toString());
        self::assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'))->__toString());
        self::assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'))->__toString());
        self::assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        self::assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'), false)->__toString());
        self::assertSame('http://foo.bar.com/path/?query', Url::parse('https://foo.bar.com/path/?query')->withScheme(Scheme::parse('http'))->__toString());
        self::assertSame('http://foo.bar.com/path/#fragment', Url::parse('https://foo.bar.com/path/#fragment')->withScheme(Scheme::parse('http'))->__toString());
        self::assertSame('http://foo.bar.com/path/?query#fragment', Url::parse('https://foo.bar.com/path/?query#fragment')->withScheme(Scheme::parse('http'))->__toString());
    }

    /**
     * Test getHost method.
     */
    public function testGetHost()
    {
        self::assertSame('foo.bar.com', Url::parse('http://foo.bar.com/path/')->getHost()->__toString());
        self::assertSame('10.10.10.10', Url::parse('http://10.10.10.10/')->getHost()->__toString());
        self::assertSame('10.10.10.10', Url::parse('http://10.10.10.10')->getHost()->__toString());
    }

    /**
     * Test that empty host is invalid.
     */
    public function testEmptyHostIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "https://" is invalid: Host "" is empty.');

        Url::parse('https://');
    }

    /**
     * Test that invalid host is invalid.
     */
    public function testInvalidHostIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "https://foo[bar" is invalid: Host "foo[bar" is invalid: Hostname "foo[bar" is invalid: Part of domain "foo[bar" contains invalid character "[".');

        Url::parse('https://foo[bar');
    }

    /**
     * Test withHost method.
     */
    public function testWithHost()
    {
        self::assertSame('http://foo.org/path/', Url::parse('http://192.168.0.1/path/')->withHost(Host::parse('foo.org'))->__toString());
        self::assertSame('http://foo.org/path/?query', Url::parse('http://192.168.0.1/path/?query')->withHost(Host::parse('foo.org'))->__toString());
        self::assertSame('http://foo.org/path/#fragment', Url::parse('http://192.168.0.1/path/#fragment')->withHost(Host::parse('foo.org'))->__toString());
        self::assertSame('http://foo.org/path/?query#fragment', Url::parse('http://192.168.0.1/path/?query#fragment')->withHost(Host::parse('foo.org'))->__toString());
    }

    /**
     * Test getPort method.
     */
    public function testGetPort()
    {
        self::assertSame(80, Url::parse('http://foo.bar.com/path/')->getPort());
        self::assertSame(443, Url::parse('https://foo.bar.com/path/')->getPort());
        self::assertSame(1000, Url::parse('https://foo.bar.com:1000/path/')->getPort());
    }

    /**
     * Test parse url with empty port.
     */
    public function testParseUrlWithEmptyPort()
    {
        self::assertSame('http://foo.com/', Url::parse('http://foo.com:')->__toString());
        self::assertSame('http://foo.com/bar/baz', Url::parse('http://foo.com:/bar/baz')->__toString());
    }

    /**
     * Test that url with invalid character in port is invalid.
     */
    public function testUrlWithInvalidCharacterInPortIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com:12X45" is invalid: Port "12X45" contains invalid character "X".');

        Url::parse('http://domain.com:12X45');
    }

    /**
     * Test that url with port out of range is invalid.
     */
    public function testUrlWithPortOutOfRangeIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com:65536" is invalid: Port 65536 is out of range: Maximum port number is 65535.');

        Url::parse('http://domain.com:65536');
    }

    /**
     * Test getPath method.
     */
    public function testGetPath()
    {
        self::assertSame('/', Url::parse('http://foo.com/')->getPath()->__toString());
        self::assertSame('/foo/bar', Url::parse('http://domain.com/foo/bar')->getPath()->__toString());
    }

    /**
     * Test that url with invalid path is invalid.
     */
    public function testUrlWithInvalidPathIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "https://domain.com:1000/foo/{bar}" is invalid: Url path "/foo/{bar}" is invalid: Filename "{bar}" contains invalid character "{".');

        Url::parse('https://domain.com:1000/foo/{bar}');
    }

    /**
     * Test parse url with invalid query string.
     */
    public function testParseWithInvalidQueryString()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "https://domain.com:1000/foo?{bar}" is invalid: Query string "{bar}" contains invalid character "{".');

        Url::parse('https://domain.com:1000/foo?{bar}');
    }

    /**
     * Test parse url with invalid query fragment.
     */
    public function testParseWithInvalidFragment()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "https://domain.com:1000/foo#{bar}" is invalid: Fragment "{bar}" contains invalid character "{".');

        Url::parse('https://domain.com:1000/foo#{bar}');
    }

    /**
     * Test getQueryString method.
     */
    public function testGetQueryString()
    {
        self::assertNull(Url::parse('http://domain.com/foo')->getQueryString());
        self::assertSame('', Url::parse('http://domain.com/foo?')->getQueryString());
        self::assertSame('bar=baz', Url::parse('http://domain.com/foo?bar=baz')->getQueryString());
        self::assertSame('F%7Baz%7D', Url::parse('http://domain.com/foo?F%7Baz%7D')->getQueryString());
    }

    /**
     * Test getFragment method.
     */
    public function testGetFragment()
    {
        self::assertNull(Url::parse('http://domain.com/foo')->getFragment());
        self::assertSame('', Url::parse('http://domain.com/foo#')->getFragment());
        self::assertSame('bar=baz', Url::parse('http://domain.com/foo#bar=baz')->getFragment());
        self::assertSame('F%7Baz%7D', Url::parse('http://domain.com/foo#F%7Baz%7D')->getFragment());
    }

    /**
     * Test fromParts method.
     */
    public function testFromParts()
    {
        self::assertSame('https://www.domain.com:1000/foo/bar?query#fragment', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), 1000, UrlPath::parse('/foo/bar'), 'query', 'fragment')->__toString());
        self::assertSame('https://www.domain.com:1000/foo/bar#fragment', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), 1000, UrlPath::parse('/foo/bar'), null, 'fragment')->__toString());
        self::assertSame('https://www.domain.com:1000/foo/bar?query', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), 1000, UrlPath::parse('/foo/bar'), 'query')->__toString());
        self::assertSame('https://www.domain.com/foo/bar?query', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'), 'query')->__toString());
        self::assertSame('https://www.domain.com/foo/bar?', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'), '')->__toString());
        self::assertSame('https://www.domain.com/foo/bar', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), null, UrlPath::parse('/foo/bar'))->__toString());
        self::assertSame('https://www.domain.com:1000/', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'), 1000)->__toString());
        self::assertSame('https://www.domain.com/', Url::fromParts(Scheme::parse('https'), Host::parse('www.domain.com'))->__toString());
    }

    /**
     * Test that using fromParts method with port number below 0 is invalid.
     */
    public function testFromPartsWithPortNumberBelow0IsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Port -1 is out of range: Minimum port number is 0.');

        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), -1, UrlPath::parse('/'));
    }

    /**
     * Test that using fromParts method with port number above 65535 is invalid.
     */
    public function testFromPartsWithPortNumberAbove65535IsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Port 65536 is out of range: Maximum port number is 65535.');

        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), 65536, UrlPath::parse('/'));
    }

    /**
     * Test that using fromParts method with relative url path is invalid.
     */
    public function testFromPartsWithRelativeUrlPathIsInvalid()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url path "foo/" is relative.');

        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), null, UrlPath::parse('foo/'));
    }

    /**
     * Test fromParts method with invalid query string.
     */
    public function testFromPartsWithInvalidQueryString()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Query string "foo#bar" contains invalid character "#".');

        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), null, UrlPath::parse('/'), 'foo#bar');
    }

    /**
     * Test fromParts method with invalid fragment.
     */
    public function testFromPartsWithInvalidFragment()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Fragment ">bar" contains invalid character ">".');

        Url::fromParts(Scheme::parse('http'), Host::parse('www.domain.com'), null, UrlPath::parse('/'), 'foo', '>bar');
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertFalse(Url::isValid(''));
        self::assertFalse(Url::isValid('foo://bar.com/'));
        self::assertTrue(Url::isValid('http://domain.com/'));
        self::assertFalse(Url::isValid('http:///path/'));
        self::assertFalse(Url::isValid('http://+++/'));
        self::assertFalse(Url::isValid('http://domain.com:XXX/'));
        self::assertTrue(Url::isValid('http://domain.com:1234/'));
        self::assertFalse(Url::isValid('http://domain.com:1234/{foo}'));
        self::assertTrue(Url::isValid('http://domain.com/foo?bar'));
        self::assertFalse(Url::isValid('http://domain.com/foo?{bar}'));
        self::assertTrue(Url::isValid('http://domain.com/?bar'));
        self::assertTrue(Url::isValid('http://domain.com/#bar'));
        self::assertTrue(Url::isValid('http://domain.com/?foo#bar'));
        self::assertFalse(Url::isValid('http://domain.com/?foo#>bar'));
        self::assertFalse(Url::isValid('http://domain.com/#bar<'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertNull(Url::tryParse(''));
        self::assertNull(Url::tryParse('foo://bar.com/'));
        self::assertSame('http://domain.com/', Url::tryParse('http://domain.com/')->__toString());
        self::assertNull(Url::tryParse('http:///path/'));
        self::assertNull(Url::tryParse('http://+++/'));
        self::assertNull(Url::tryParse('http://domain.com:XXX/'));
        self::assertSame('http://domain.com:1234/', Url::tryParse('http://domain.com:1234/')->__toString());
        self::assertNull(Url::tryParse('http://domain.com:1234/{foo}'));
        self::assertSame('http://domain.com/foo?bar', Url::tryParse('http://domain.com/foo?bar')->__toString());
        self::assertNull(Url::tryParse('http://domain.com/foo?{bar}'));
        self::assertSame('http://domain.com/?bar', Url::tryParse('http://domain.com/?bar')->__toString());
        self::assertSame('http://domain.com/#bar', Url::tryParse('http://domain.com/#bar')->__toString());
        self::assertSame('http://domain.com/?foo#bar', Url::tryParse('http://domain.com/?foo#bar')->__toString());
        self::assertNull(Url::tryParse('http://domain.com/?foo#>bar'));
        self::assertNull(Url::tryParse('http://domain.com/#bar<'));
    }

    /**
     * Test parseRelative method.
     */
    public function testParseRelative()
    {
        $url = Url::parse('http://foo.com:8080/path/file?query#fragment');

        self::assertSame('https://bar.com/new-path/new-file?new-query#new-fragment', Url::parseRelative('https://bar.com/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://bar.com/new-path/new-file?new-query#new-fragment', Url::parseRelative('//bar.com/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-path/new-file?new-query#new-fragment', Url::parseRelative('/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file?new-query#new-fragment', Url::parseRelative('new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-file?new-query#new-fragment', Url::parseRelative('../new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?new-query#new-fragment', Url::parseRelative('?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?query#new-fragment', Url::parseRelative('#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?new-query', Url::parseRelative('?new-query', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file', Url::parseRelative('new-file', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file#new-fragment', Url::parseRelative('new-file#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-path/', Url::parseRelative('/new-path/', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?query#fragment', Url::parseRelative('', $url)->__toString());
    }

    /**
     * Test parseRelative method with empty scheme.
     */
    public function testParseRelativeWithEmptyScheme()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "://domain.com/" is invalid: Scheme "" is empty.');

        Url::parseRelative('://domain.com/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid scheme.
     */
    public function testParseRelativeWithInvalidScheme()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "baz://domain.com/" is invalid: Scheme "baz" is invalid: Scheme must be "http" or "https".');

        Url::parseRelative('baz://domain.com/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid host.
     */
    public function testParseRelativeWithInvalidHost()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://[domain].com/" is invalid: Host "[domain].com" is invalid: Hostname "[domain].com" is invalid: Part of domain "[domain]" contains invalid character "[".');

        Url::parseRelative('http://[domain].com/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid port.
     */
    public function testParseRelativeWithInvalidPort()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com:foo/" is invalid: Port "foo" contains invalid character "f".');

        Url::parseRelative('http://domain.com:foo/', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid path.
     */
    public function testParseRelativeWithInvalidPath()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com/{path}" is invalid: Url path "/{path}" is invalid: Filename "{path}" contains invalid character "{".');

        Url::parseRelative('http://domain.com/{path}', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid query string.
     */
    public function testParseRelativeWithInvalidQueryString()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com/path?{query}" is invalid: Query string "{query}" contains invalid character "{".');

        Url::parseRelative('http://domain.com/path?{query}', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parseRelative method with invalid fragment.
     */
    public function testParseRelativeWithInvalidFragment()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Url "http://domain.com/path#{fragment}" is invalid: Fragment "{fragment}" contains invalid character "{".');

        Url::parseRelative('http://domain.com/path#{fragment}', Url::parse('http://foo.com:8080/path/file?query'));
    }

    /**
     * Test parse relative path with path that resolves above root level.
     */
    public function testParseRelativePathWithPathAboveRootLevel()
    {
        self::expectException(UrlPathLogicException::class);
        self::expectExceptionMessage('Url path "/foo/" can not be combined with url path "../../bar": Absolute path is above root level.');

        echo Url::parseRelative('../../bar', Url::parse('http://localhost/foo/'));
    }

    /**
     * Test getHostAndPort method.
     */
    public function testGetHostAndPort()
    {
        self::assertSame('foo.bar.com', Url::parse('http://foo.bar.com/')->getHostAndPort());
        self::assertSame('foo.bar.com', Url::parse('https://foo.bar.com/')->getHostAndPort());
        self::assertSame('foo.bar.com:80', Url::parse('https://foo.bar.com:80/')->getHostAndPort());
    }

    /**
     * Test withPath method.
     */
    public function testWithPath()
    {
        self::assertSame('https://localhost/', Url::parse('https://localhost/foo/bar')->withPath(UrlPath::parse('/'))->__toString());
        self::assertSame('https://localhost/baz.html', Url::parse('https://localhost/foo/bar')->withPath(UrlPath::parse('/baz.html'))->__toString());
        self::assertSame('https://localhost/foo/', Url::parse('https://localhost/foo/bar')->withPath(UrlPath::parse(''))->__toString());
        self::assertSame('https://localhost/', Url::parse('https://localhost/foo/bar')->withPath(UrlPath::parse('..'))->__toString());
        self::assertSame('https://localhost/?query', Url::parse('https://localhost/foo/bar?query')->withPath(UrlPath::parse('/'))->__toString());
        self::assertSame('https://localhost/#fragment', Url::parse('https://localhost/foo/bar#fragment')->withPath(UrlPath::parse('/'))->__toString());
        self::assertSame('https://localhost/?query#fragment', Url::parse('https://localhost/foo/bar?query#fragment')->withPath(UrlPath::parse('/'))->__toString());
    }

    /**
     * Test withPath method with path that resolves above root level.
     */
    public function testWithUrlPathWithPathAboveRootLevel()
    {
        self::expectException(UrlPathLogicException::class);
        self::expectExceptionMessage('Url path "/foo/bar" can not be combined with url path "../../": Absolute path is above root level.');

        Url::parse('https://localhost.com/foo/bar')->withPath(UrlPath::parse('../../'));
    }

    /**
     * Test withPort method.
     */
    public function testWithPort()
    {
        self::assertSame('http://localhost:81/', Url::parse('http://localhost/')->withPort(81)->__toString());
        self::assertSame('http://localhost/', Url::parse('http://localhost:81/')->withPort(80)->__toString());
        self::assertSame('https://localhost/', Url::parse('https://localhost:80/')->withPort(443)->__toString());
        self::assertSame('http://localhost:81/?query', Url::parse('http://localhost/?query')->withPort(81)->__toString());
        self::assertSame('http://localhost:81/#fragment', Url::parse('http://localhost/#fragment')->withPort(81)->__toString());
        self::assertSame('http://localhost:81/?query#fragment', Url::parse('http://localhost/?query#fragment')->withPort(81)->__toString());
    }

    /**
     * Test withPort method with port below 0.
     */
    public function testWithPortWithPortBelow0()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Port -1 is out of range: Minimum port number is 0.');

        Url::parse('http://localhost/')->withPort(-1);
    }

    /**
     * Test withPort method with port above 65535.
     */
    public function testWithPortWithPortAbove65535()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Port 65536 is out of range: Maximum port number is 65535.');

        Url::parse('http://localhost/')->withPort(65536);
    }

    /**
     * Test withQueryString method.
     */
    public function testWithQueryString()
    {
        self::assertSame('https://domain.com/foo?bar', Url::parse('https://domain.com/foo')->withQueryString('bar')->__toString());
        self::assertSame('https://domain.com/foo?baz', Url::parse('https://domain.com/foo?bar')->withQueryString('baz')->__toString());
        self::assertSame('https://domain.com/foo?baz#fragment', Url::parse('https://domain.com/foo?bar#fragment')->withQueryString('baz')->__toString());
        self::assertSame('https://domain.com/foo', Url::parse('https://domain.com/foo')->withQueryString()->__toString());
        self::assertSame('https://domain.com/foo', Url::parse('https://domain.com/foo?bar')->withQueryString()->__toString());
        self::assertSame('https://domain.com/foo#fragment', Url::parse('https://domain.com/foo?bar#fragment')->withQueryString()->__toString());
    }

    /**
     * Test withQueryString method with invalid query string.
     */
    public function testWithQueryStringWithInvalidQueryString()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Query string "{foo}" contains invalid character "{".');

        Url::parse('https://domain.com/')->withQueryString('{foo}');
    }

    /**
     * Test withFragment method.
     */
    public function testWithFragment()
    {
        self::assertSame('https://domain.com/foo#bar', Url::parse('https://domain.com/foo')->withFragment('bar')->__toString());
        self::assertSame('https://domain.com/foo#baz', Url::parse('https://domain.com/foo#bar')->withFragment('baz')->__toString());
        self::assertSame('https://domain.com/foo?bar#baz', Url::parse('https://domain.com/foo?bar#fragment')->withFragment('baz')->__toString());
        self::assertSame('https://domain.com/foo', Url::parse('https://domain.com/foo')->withFragment()->__toString());
        self::assertSame('https://domain.com/foo', Url::parse('https://domain.com/foo#bar')->withFragment()->__toString());
        self::assertSame('https://domain.com/foo?bar', Url::parse('https://domain.com/foo?bar#fragment')->withFragment()->__toString());
    }

    /**
     * Test withFragment method with invalid fragment.
     */
    public function testWithFragmentWithInvalidFragment()
    {
        self::expectException(UrlInvalidArgumentException::class);
        self::expectExceptionMessage('Fragment "{foo}" contains invalid character "{".');

        Url::parse('https://domain.com/')->withFragment('{foo}');
    }

    /**
     * Test tryParseRelative method.
     */
    public function testTryParseRelative()
    {
        $url = Url::parse('http://foo.com:8080/path/file?query#fragment');

        self::assertSame('https://bar.com/new-path/new-file?new-query#new-fragment', Url::tryParseRelative('https://bar.com/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://bar.com/new-path/new-file?new-query#new-fragment', Url::tryParseRelative('//bar.com/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-path/new-file?new-query#new-fragment', Url::tryParseRelative('/new-path/new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file?new-query#new-fragment', Url::tryParseRelative('new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-file?new-query#new-fragment', Url::tryParseRelative('../new-file?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?new-query#new-fragment', Url::tryParseRelative('?new-query#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?query#new-fragment', Url::tryParseRelative('#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?new-query', Url::tryParseRelative('?new-query', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file', Url::tryParseRelative('new-file', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/new-file#new-fragment', Url::tryParseRelative('new-file#new-fragment', $url)->__toString());
        self::assertSame('http://foo.com:8080/new-path/', Url::tryParseRelative('/new-path/', $url)->__toString());
        self::assertSame('http://foo.com:8080/path/file?query#fragment', Url::tryParseRelative('', $url)->__toString());
        self::assertNull(Url::tryParseRelative('://domain.com/', $url));
        self::assertNull(Url::tryParseRelative('baz://domain.com/', $url));
        self::assertNull(Url::tryParseRelative('http://[domain].com/', $url));
        self::assertNull(Url::tryParseRelative('http://domain.com:foo/', $url));
        self::assertNull(Url::tryParseRelative('http://domain.com/{path}', $url));
        self::assertNull(Url::tryParseRelative('http://domain.com/path?{query}', $url));
        self::assertNull(Url::tryParseRelative('http://domain.com/path#{fragment}', $url));
        self::assertNull(Url::tryParseRelative('../../', $url));
    }

    /**
     * Test isValidRelative method.
     */
    public function testIsValidRelative()
    {
        $url = Url::parse('http://foo.com:8080/path/file?query#fragment');

        self::assertTrue(Url::isValidRelative('https://bar.com/new-path/new-file?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('//bar.com/new-path/new-file?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('/new-path/new-file?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('new-file?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('../new-file?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('?new-query#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('?new-query', $url));
        self::assertTrue(Url::isValidRelative('new-file', $url));
        self::assertTrue(Url::isValidRelative('new-file#new-fragment', $url));
        self::assertTrue(Url::isValidRelative('/new-path/', $url));
        self::assertTrue(Url::isValidRelative('', $url));
        self::assertFalse(Url::isValidRelative('://domain.com/', $url));
        self::assertFalse(Url::isValidRelative('baz://domain.com/', $url));
        self::assertFalse(Url::isValidRelative('http://[domain].com/', $url));
        self::assertFalse(Url::isValidRelative('http://domain.com:foo/', $url));
        self::assertFalse(Url::isValidRelative('http://domain.com/{path}', $url));
        self::assertFalse(Url::isValidRelative('http://domain.com/path?{query}', $url));
        self::assertFalse(Url::isValidRelative('http://domain.com/path#{fragment}', $url));
        self::assertFalse(Url::isValidRelative('../../', $url));
    }

    /**
     * Test parse method with special characters.
     */
    public function testParseWithSpecialCharacters()
    {
        $url = Url::parse("http://example.com/:@-._~!$&'()*+,=;:@-._~!$&'()*+,=:@-._~!$&'()*+,==?/?:@-._~!$'()*+,;=/?:@-._~!$'()*+,;==#/?:@-._~!$&'()*+,;=");

        self::assertSame('http', $url->getScheme()->__toString());
        self::assertSame(80, $url->getPort());
        self::assertSame('example.com', $url->getHost()->__toString());
        self::assertSame(0, count($url->getPath()->getDirectoryParts()));
        self::assertSame(":@-._~!$&'()*+,=;:@-._~!$&'()*+,=:@-._~!$&'()*+,==", $url->getPath()->getFilename());
        self::assertSame("/?:@-._~!$'()*+,;=/?:@-._~!$'()*+,;==", $url->getQueryString());
        self::assertSame("/?:@-._~!$&'()*+,;=", $url->getFragment());
        self::assertSame(
            'http://example.com' .
            '/%3A%40-._~%21%24%26%27%28%29%2A%2B%2C%3D%3B%3A%40-._~%21%24%26%27%28%29%2A%2B%2C%3D%3A%40-._~%21%24%26%27%28%29%2A%2B%2C%3D%3D' .
            '?/?:@-._~!$\'()*+,;=/?:@-._~!$\'()*+,;==' .
            '#/?:@-._~!$&\'()*+,;=',
            $url->__toString()
        );
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(Url::parse('https://example.com/')->equals(Url::parse('https://example.com/')));
        self::assertTrue(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.com:8080/path/file?query#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('http://example.com:8080/path/file?query#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.org:8080/path/file?query#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.com/path/file?query#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.com:8080/file?query#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.com:8080/path/file#fragment')));
        self::assertFalse(Url::parse('https://example.com:8080/path/file?query#fragment')->equals(Url::parse('https://example.com:8080/path/file?query#foo')));
    }

    /**
     * Test getPathAndQueryString method.
     */
    public function testGetPathAndQueryString()
    {
        self::assertSame('/', Url::parse('http://example.com/')->getPathAndQueryString());
        self::assertSame('/foo', Url::parse('http://example.com/foo')->getPathAndQueryString());
        self::assertSame('/?', Url::parse('http://example.com/?')->getPathAndQueryString());
        self::assertSame('/foo?', Url::parse('http://example.com/foo?')->getPathAndQueryString());
        self::assertSame('/?bar', Url::parse('http://example.com/?bar')->getPathAndQueryString());
        self::assertSame('/foo?bar', Url::parse('http://example.com/foo?bar')->getPathAndQueryString());
        self::assertSame('/?bar=F%7Baz%7D', Url::parse('http://example.com/?bar=F%7Baz%7D')->getPathAndQueryString());
        self::assertSame('/foo?bar=F%7Baz%7D', Url::parse('http://example.com/foo?bar=F%7Baz%7D')->getPathAndQueryString());
    }
}
