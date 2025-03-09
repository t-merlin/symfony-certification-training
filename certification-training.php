#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Command\LaunchTrainingCommand;
use Symfony\Component\Console\Application;

const VERSION = 7.2;
const APPLICATION_NAME = 'Symfony Certification Training';

$application = new Application(APPLICATION_NAME, VERSION);

$startCommand = new LaunchTrainingCommand();
$application->add($startCommand);
$application->setDefaultCommand($startCommand->getName());
try {
    $application->run();
} catch (Exception $e) {
    echo $e->getMessage();
}