<?php

declare(strict_types=1);

namespace NiR\FCGIClient\Command;

use hollodotme\FastCGI\Requests\AbstractRequest;
use hollodotme\FastCGI\Requests\PostRequest;

final class PostCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('POST')
            ->setAliases(['post'])
            ->setDescription('Send a POST request to a FastCGI server.')
        ;
    }

    protected function instantiateRequest(string $scriptFilename, string $queries): AbstractRequest
    {
        return new PostRequest($scriptFilename, $queries);
    }
}
