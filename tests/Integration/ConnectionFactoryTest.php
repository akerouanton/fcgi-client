<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Tests\Integration;

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Requests\GetRequest;
use NiR\FCGIClient\ConnectionFactory;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    public function testTcpIpBasedConnection()
    {
        $client = new Client(ConnectionFactory::createSocket('localhost:9000', 1000));

        $response = $client->sendRequest(new GetRequest('', ''));
        $this->assertNotFalse($response); // Junk assertion, if we're up to that point everything's alright
    }

    public function testUnixSocketBasedConnection()
    {
        $client = new Client(ConnectionFactory::createSocket('/var/run/php-fpm.sock', 1000));

        $response = $client->sendRequest(new GetRequest('', ''));
        $this->assertNotFalse($response); // Junk assertion, if we're up to that point everything's alright
    }
}
