<?php

namespace NiR\FCGIClient\Command;

use Cilex\Provider\Console\Command;
use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\SocketConnections\NetworkSocket;
use hollodotme\FastCGI\SocketConnections\UnixDomainSocket;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument('server', InputArgument::REQUIRED, 'ip:port of the FCGI server or path to valid unix socket.')
            ->addArgument('script', InputArgument::REQUIRED, 'Path of the script to execute.')
            ->addArgument('query-string', InputArgument::OPTIONAL|InputArgument::IS_ARRAY, 'Query string sent to the FastCGI server.')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, '', 5000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client  = $this->getClient($input);
        $request = $this->getRequest($input);

        $response = $client->sendRequest($request);
        echo $response->getBody();
    }

    abstract protected function getRequest(InputInterface $input);

    /**
     * @param InputInterface $input
     *
     * @return Client
     *
     * @throws \InvalidArgumentException If the server found in CLI arguments is invalid.
     */
    protected function getClient(InputInterface $input): Client
    {
        $server = $input->getArgument('server');
        $timeout = $input->getOption('timeout');

        // @TODO: Add support for IPv6
        if (strpos($server, ':') !== false) {
            list($ip, $port) = explode(':', $server);
            return new Client(new NetworkSocket($ip, $port, $timeout, $timeout));
        }

        if (file_exists($server)) {
            return new Client(new UnixDomainSocket($server));
        }

        throw new \InvalidArgumentException('The "server" value provided is neither a valid ip:port nor a vaid UNIX socket.');
    }
}
