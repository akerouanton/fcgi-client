<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Requests\AbstractRequest;
use hollodotme\FastCGI\Requests\PutRequest;

final class PutCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('PUT')
            ->setAliases(['put'])
            ->setDescription('Send a PUT request to a FastCGI server.')
        ;
    }

    protected function instantiateRequest(string $scriptFilename, string $queries): AbstractRequest
    {
        return new PutRequest($scriptFilename, $queries);
    }
}
