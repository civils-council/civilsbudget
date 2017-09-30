<?php

require __DIR__ . '/base_script.php';

show_run("Drop DB", "bin/console doctrine:database:drop --force");
show_run("Create DB", "bin/console doctrine:database:create");
show_run("Create scheme", "bin/console doctrine:schema:create");
show_run("Install assets", "bin/console assets:install");
show_run("Install assets", "bin/console assets:install web --symlink");

show_run("Load fixtures", "bin/console doctrine:fixtures:load --no-interaction");