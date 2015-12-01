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

show_run("Destroying cache dir manually", "rm -rf app/cache/*");
show_run("Creating directories for app kernel", "mkdir -p app/cache/dev app/cache/prod app/cache/test app/logs");
show_run("Warming up dev cache", "php app/console --env=dev cache:clear");
show_run("Warming up prod cache", "php app/console --env=prod cache:clear");
show_run("Changing permissions", "chmod -R 777 app/cache app/logs");

exit(0);
