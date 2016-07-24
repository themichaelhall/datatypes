<?php

use DataTypes\Exceptions\UrlPathLogicException;

/**
 * Test UrlPathLogicException class.
 */
class UrlPathLogicExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that UrlPathLogicException is subclass of LogicException.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage This is a UrlPathLogicException.
     */
    public function testUrlPathLogicExceptionIsLogicException()
    {
        throw new UrlPathLogicException('This is a UrlPathLogicException.');
    }
}
