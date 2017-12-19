<?php

declare(strict_types=1);

namespace NiR\FCGIClient;

use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\SocketConnections\NetworkSocket;
use hollodotme\FastCGI\SocketConnections\UnixDomainSocket;

final class ConnectionFactory
{
    /**
     * @param string $server
     * @param int    $timeout
     *
     * @return ConfiguresSocketConnection
     *
     * @throws \InvalidArgumentException
     */
    public static function createSocket(string $server, int $timeout): ConfiguresSocketConnection
    {
        // @TODO: Add support for IPv6
        if (strpos($server, ':') !== false && file_exists($server) === false) {
            list($ip, $port) = explode(':', $server);

            return new NetworkSocket($ip, intval($port), $timeout, $timeout);
        }

        if (file_exists($server)) {
            return new UnixDomainSocket($server, $timeout, $timeout);
        }

        throw new \InvalidArgumentException('The server value provided is neither a valid IP:port nor a valid UNIX socket path.');
    }
}
