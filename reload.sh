#!/bin/bash

echo ""
echo "Choices:"
echo "    1 - INSTALL"
echo "    2 - RELOAD"
echo "    3 - RELOAD FIXTURES"
echo "    4 - TESTS"
echo "    0 - exit"
read reload

case $reload in
1)
    echo "run INSTALL"
    npm install
    ./node_modules/.bin/bower install

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
    rm -rf composer.phar

    ./node_modules/.bin/gulp
    ./app/console doctrine:database:create
    ./app/console doctrine:migration:migrate -n

;;
2)
    echo "run RELOAD"
    npm install
    ./node_modules/.bin/bower install

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
    rm -rf composer.phar

    ./node_modules/.bin/gulp

    ./app/console cache:clear
    ./app/console cache:clear -e prod

;;
3)
    echo "load Fixtures"
    ./app/console doctrine:fixtures:load --no-interaction

;;
4)
    echo "run TESTS"
    ./bin/phpunit -c app

;;
0)
    exit 0
;;
*)
    echo "Change correctly task!!!"
    sh ./reload.sh

esac
