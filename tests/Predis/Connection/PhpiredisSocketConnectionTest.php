<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use Predis\Command\RawCommand;
use Predis\Response\Error as ErrorResponse;

/**
 * @group ext-phpiredis
 * @requires extension phpiredis
 */
class PhpiredisSocketConnectionTest extends PredisConnectionTestCase
{
    const CONNECTION_CLASS = 'Predis\Connection\PhpiredisSocketConnection';

    /**
     * @group disconnected
     */
    public function testSupportsSchemeTls()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("Invalid scheme: 'tls'");

        $connection = $this->createConnectionWithParams(array('scheme' => 'tls'));

        $this->assertInstanceOf('Predis\Connection\NodeConnectionInterface', $connection);
    }

    /**
     * @group disconnected
     */
    public function testSupportsSchemeRediss()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("Invalid scheme: 'rediss'");

        $connection = $this->createConnectionWithParams(array('scheme' => 'rediss'));

        $this->assertInstanceOf('Predis\Connection\NodeConnectionInterface', $connection);
    }

    /**
     * @group disconnected
     */
    public function testThrowsExceptionOnInitializationCommandFailure()
    {
        $this->expectException('Predis\Connection\ConnectionException');
        $this->expectExceptionMessage("`SELECT` failed: ERR invalid DB index [tcp://127.0.0.1:6379]");

        $cmdSelect = RawCommand::create('SELECT', '1000');

        $connection = $this->getMockBuilder(static::CONNECTION_CLASS)
                           ->setMethods(array('executeCommand', 'createResource'))
                           ->setConstructorArgs(array(new Parameters()))
                           ->getMock();

        $connection->method('executeCommand')
                   ->with($cmdSelect)
                   ->will($this->returnValue(
                       new ErrorResponse('ERR invalid DB index')
                   ));

        $connection->method('createResource');

        $connection->addConnectCommand($cmdSelect);
        $connection->connect();
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testThrowsExceptionOnUnresolvableHostname()
    {
        $this->expectException('Predis\Connection\ConnectionException');
        $this->expectExceptionMessage("Cannot resolve the address of 'bogus.tld'");

        $connection = $this->createConnectionWithParams(array('host' => 'bogus.tld'));
        $connection->connect();
    }

    /**
     * @medium
     * @group connected
     */
    public function testThrowsExceptionOnProtocolDesynchronizationErrors()
    {
        $this->expectException('Predis\Protocol\ProtocolException');

        $connection = $this->createConnection();
        $socket = $connection->getResource();

        $connection->writeRequest($this->getCurrentProfile()->createCommand('ping'));
        socket_read($socket, 1);

        $connection->read();
    }
}
