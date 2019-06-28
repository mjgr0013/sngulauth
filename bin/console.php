<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Sngular\Auth\Provider\Keycloak\Command\ConnectAuthStartCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new ConnectAuthStartCommand());

$application->run();