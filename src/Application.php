<?php

namespace NiR\FCGIClient;

use Cilex\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('fcgi-client', '0.1.0');

        $this->command(new Command\GetCommand());
    }
}
