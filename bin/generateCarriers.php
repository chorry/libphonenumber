<?php

require_once __DIR__ . '/../vendor/autoload.php';


use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \chr\phoneNumber\tools\GenerateCarriersCommand());
$application->run();
