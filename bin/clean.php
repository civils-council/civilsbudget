#!/usr/bin/env php
<?php

function show_run($text, $command, $canFail = false)
{
    echo "\n* $text\n$command\n";
    passthru($command, $return);
    if (0 !== $return && !$canFail) {
        echo "\n/!\\ The command returned $return\n";
        exit(1);
    }
}

show_run("Destroying cache dir manually", "rm -rf var/cache/*");
show_run("Creating directories for app kernel", "mkdir -p var/cache/dev var/cache/prod var/cache/test var/logs");
show_run("Warming up dev cache", "bin/console --env=dev cache:clear");
show_run("Warming up prod cache", "bin/console --env=prod cache:clear");
show_run("Changing permissions", "chmod -R 777 var/cache var/logs");

exit(0);
