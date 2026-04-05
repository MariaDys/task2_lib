<?php

use Symfony\Component\Process\Process;

$allowed = ['ls'];

$cmd = 'ls';
$arg = 'test';

if (!in_array($cmd, $allowed)) {
    exit;
}

$process = new Process([$cmd, $arg]);
$process->setTimeout(10);
$process->run();

if (!$process->isSuccessful()) {
    error_log($process->getErrorOutput());
    throw new Exception("Command failed");
}
