<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Requests\AbstractRequest;
use hollodotme\FastCGI\Requests\PatchRequest;

final class PatchCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('PATCH')
            ->setAliases(['patch'])
            ->setDescription('Send a PATCH request to a FastCGI server.')
        ;
    }

    protected function instantiateRequest(string $scriptFilename, string $queries): AbstractRequest
    {
        return new PatchRequest($scriptFilename, $queries);
    }
}
