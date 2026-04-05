<?php

use Symfony\Component\Process\Process;

Process::fromShellCommandline("ls " . $_GET['dir']); //shell

new Process("ls " . $_GET['dir']); //строковая команда

new Process([$_GET['cmd'], 'arg']); //пользователь управляет командой

$cmd = $_GET['cmd']; // нет whitelist
new Process([$cmd, 'test']);

echo $process->getErrorOutput(); // утечка stderr

$process->setTimeout(null); // нет timeout