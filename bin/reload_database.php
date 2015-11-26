<?php

require __DIR__ . '/base_script.php';

show_run("Drop DB", "php app/console doctrine:database:drop --force");
show_run("Create DB", "php app/console doctrine:database:create");
show_run("Create scheme", "php app/console doctrine:schema:create");
show_run("Install assets", "php app/console assets:install");
show_run("Install assets", "php app/console assets:install web --symlink");

show_run("Load fixtures", "php app/console doctrine:fixtures:load --no-interaction");