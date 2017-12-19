<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Requests\AbstractRequest;
use hollodotme\FastCGI\Requests\DeleteRequest;

final class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('DELETE')
            ->setAliases(['delete'])
            ->setDescription('Send a DELETE request to a FastCGI server.')
        ;
    }

    protected function instantiateRequest(string $scriptFilename, string $queries): AbstractRequest
    {
        return new DeleteRequest($scriptFilename, $queries);
    }
}
