<?php

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Requests\GetRequest;
use Symfony\Component\Console\Input\InputInterface;

class GetCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('get')
            ->setAliases(['GET'])
            ->setDescription('Send a GET request.')
        ;
    }

    protected function getRequest(InputInterface $input)
    {
        $scriptName = $input->getArgument('script');
        $queryString = $input->getArgument('query-string');

        $request = new GetRequest($scriptName, http_build_query($queryString));

        $request->setCustomVar('SCRIPT_NAME', $scriptName);
        $request->setCustomVar('QUERY_STRING', implode('&', $queryString));

        return $request;
    }
}
