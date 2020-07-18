<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use hollodotme\FastCGI\Requests\AbstractRequest;
use hollodotme\FastCGI\Requests\DeleteRequest;
use hollodotme\FastCGI\Requests\GetRequest;
use hollodotme\FastCGI\Requests\PatchRequest;
use hollodotme\FastCGI\Requests\PostRequest;
use hollodotme\FastCGI\Requests\PutRequest;
use NiR\FCGIClient\ConnectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument('server', InputArgument::REQUIRED, 'IP:port of the FCGI server or file path to valid UNIX socket.')
            ->addArgument('script-filename', InputArgument::REQUIRED, 'Absolute path of the script to execute.')
            ->addOption('script-name', null, InputOption::VALUE_OPTIONAL, 'Filename of the script to execute (default to basename of script-filename).')
            ->addOption('qs', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Query string sent to the FastCGI server.')
            ->addOption('document-root', null, InputOption::VALUE_OPTIONAL, 'The root directory of the server.')
            ->addOption('cookie', 'c', InputOption::VALUE_OPTIONAL, 'Cookie fields sent to the FastCGI server, can be specified multiple times.')
            ->addOption('host', 'H', InputOption::VALUE_OPTIONAL)
            ->addOption('referer', 'r', InputOption::VALUE_OPTIONAL)
            ->addOption('user-agent', null, InputOption::VALUE_OPTIONAL)
            ->addOption('https', null, InputOption::VALUE_NONE)
            ->addOption('content-type', null, InputOption::VALUE_OPTIONAL)
            ->addOption('request-uri', null, InputOption::VALUE_OPTIONAL)
            ->addOption('remote-addr', null, InputOption::VALUE_OPTIONAL)
            ->addOption('remote-host', null, InputOption::VALUE_OPTIONAL)
            ->addOption('remote-port', null, InputOption::VALUE_OPTIONAL)
            ->addOption('server-addr', null, InputOption::VALUE_OPTIONAL)
            ->addOption('server-port', null, InputOption::VALUE_OPTIONAL)
            ->addOption('server-name', null, InputOption::VALUE_OPTIONAL, 'The fully qualified domain name passed to the FastCGI server.')
            ->addOption('server-protocol', null, InputOption::VALUE_OPTIONAL)
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, '', 5000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server  = $input->getArgument('server');
        $timeout = $input->getOption('timeout');

        $connection  = ConnectionFactory::createSocket($server, $timeout);
        $client  = new Client($connection);
        $request = $this->buildRequest($input);

        $response = $client->sendRequest($connection, $request);
        $rawResponse = str_replace("\r", '', explode("\n", $response->getOutput()));

        if (!empty($rawResponse) && array_shift($rawResponse) === 'Primary script unknown') {
            fwrite(STDERR, "File not found.\n");
            exit(1);
        }

        echo $response->getBody();
        exit(0);
    }

    abstract protected function instantiateRequest(string $scriptFilename, string $queries): AbstractRequest;

    private function buildRequest(InputInterface $input): AbstractRequest
    {
        $scriptFilename = $input->getArgument('script-filename');
        $scriptName     = $input->getOption('script-name');
        $queryString    = http_build_query($input->getOption('qs') ?: []);
        $documentRoot   = $input->getOption('document-root');
        $cookie         = $input->getOption('cookie');
        $host           = $input->getOption('host');
        $referer        = $input->getOption('referer');
        $userAgent      = $input->getOption('user-agent');
        $https          = $input->getOption('https');
        $contentType    = $input->getOption('content-type');
        $requestUri     = $input->getOption('request-uri');
        $remoteAddr     = $input->getOption('remote-addr');
        $remoteHost     = $input->getOption('remote-host');
        $remotePort     = $input->getOption('remote-port');
        $serverAddr     = $input->getOption('server-addr');
        $serverPort     = $input->getOption('server-port');
        $serverName     = $input->getOption('server-name');
        $serverProtocol = $input->getOption('server-protocol');

        if ($scriptName === null) {
            $scriptName = basename($scriptFilename);
        }

        $request = $this->instantiateRequest($scriptFilename, $queryString);
        $request->setCustomVar('SCRIPT_NAME', $scriptName);
        $request->setCustomVar('QUERY_STRING', $queryString);

        if ($documentRoot !== null) {
            $request->setCustomVar('DOCUMENT_ROOT', $documentRoot);
        }
        if (!empty($cookie)) {
            $request->setCustomVar('HTTP_COOKIE', http_build_cookie($cookie));
        }
        if ($host !== null) {
            $request->setCustomVar('HTTP_REFERER', $referer);
        }
        if ($userAgent !== null) {
            $request->setCustomVar('HTTP_USER_AGENT', $userAgent);
        }
        if ($https === true) {
            $request->setCustomVar('HTTPS', 'on');
        }
        if ($contentType !== null) {
            $request->setContentType($contentType);
        }
        if ($requestUri !== null) {
            $request->setRequestUri($requestUri);
        }
        if ($remoteAddr !== null) {
            $request->setRemoteAddress($remoteAddr);
        }
        if ($remoteHost !== null) {
            $request->setCustomVar('REMOTE_HOST', $remoteHost);
        }
        if ($remotePort !== null) {
            $request->setRemotePort($remotePort);
        }
        if ($serverAddr !== null) {
            $request->setServerAddress($serverAddr);
        }
        if ($serverPort !== null) {
            $request->setServerPort($serverPort);
        }
        if ($serverName !== null) {
            $request->setServerName($serverName);
        }
        if ($serverProtocol !== null) {
            $request->setServerProtocol($serverProtocol);
        }

        $request->setServerSoftware('akerouanton/fcgi-client:' . \FCGI_CLIENT_VERSION);

        return $request;
    }
}
