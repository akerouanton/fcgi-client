# FastCGI Client

A PHP CLI client for FastCGI.

# Why?

You may want to use this tool in the following cases:

* You want to execute a script on a FastCGI server without making this script accessible through HTTP calls (like
`/fpm_status` or scripts related to opcache/apcu) ;
* You can't install `cgi-fcgi` binary on your system (for instance, because of lack of privileges) ;
* A fresh server provisioning went wrong and you need to figure out which of your FastCGI or HTTP server is wrongly
configured ;
* You want to leverage PHP's share-nothing architecture and some AMQP broker (never tested though).

It's been primarily done to bypass a reeeaaaaally slow hosting support I'm working with, for one of my project at KnpLabs:
we had (and still have) no privileges to install softwares (like `cgi-fcgi`) ourselves. The end goal of this was to got
an insight about PHP-FPM workers in the day, when a specific bug occurred (workers were dying way too fast leading to
service saturation, but that's another story).

## How to install?

```bash
curl -o ~/bin/fcgi-client https://github.com/akerouanton/fcgi-client/releases/download/v0.1.0/fcgi-client.phar
chmod +x ~/bin/fcgi-client
```

## How to use?

```bash
# Call a script "opcache-clear.php" in the current directory, through a UNIX socket
fcgi-client get /var/run/php-fpm.sock $PWD/opcache-clear.php

# Note that --script-name parameter is required to access the status page because /fpm_status does not exist on your 
# filesystem and is only a "magic" script PHP-FPM knows (see pm.status_path parameter in your pool config)
fcgi-client get localhost:9000 /fpm_status --script-name /fpm_status --qs full --qs json

# If you want more details about available parameters 
fcgi-client help send
```

## Notes

Parameters are based on the list of CGI environment variables available here: http://www.cgi101.com/book/ch3/text.html.

### Run tests

```bash
$ docker run -it --rm -v $PWD/tests/fixtures/pools/:/usr/local/etc/php-fpm.d/ -v $PWD:/app -v $PWD/.docker/entrypoint:/entrypoint --entrypoint /entrypoint -w /app php:7.0-fpm vendor/bin/phpunit tests/ 
```
