<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol\Text;

use \PHPUnit_Framework_TestCase as StandardTestCase;
use Predis\ResponseQueued;

/**
 *
 */
class ResponseIntegerHandlerTest extends StandardTestCase
{
    /**
     * @group disconnected
     */
    public function testInteger()
    {
        $handler = new ResponseIntegerHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertSame(0, $handler->handle($connection, '0'));
        $this->assertSame(1, $handler->handle($connection, '1'));
        $this->assertSame(10, $handler->handle($connection, '10'));
        $this->assertSame(-10, $handler->handle($connection, '-10'));
    }

    /**
     * @group disconnected
     */
    public function testNull()
    {
        $handler = new ResponseIntegerHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertNull($handler->handle($connection, 'nil'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Cannot parse 'invalid' as a numeric response
     */
    public function testInvalid()
    {
        $handler = new ResponseIntegerHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $handler->handle($connection, 'invalid');
    }
}
