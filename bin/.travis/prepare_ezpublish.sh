#!/bin/bash

# Script to prepare eZPublish installation

echo "> Setup github auth key to not reach api limit"
cp bin/.travis/composer-auth.json ~/.composer/auth.json

echo "> Set folder permissions"
sudo find {ezpublish/{cache,logs,config,sessions},web} -type d | sudo xargs chmod -R 777
sudo find {ezpublish/{cache,logs,config,sessions},web} -type f | sudo xargs chmod -R 666

echo "> Copy behat specific parameters.yml settings"
cp bin/.travis/parameters.yml ezpublish/config/

# Switch to another Symfony version if asked for (with composer update to not use composer.lock if present)
if [ "$SYMFONY_VERSION" != "" ] ; then
    echo "> Install dependencies through Composer (with custom Symfony version: ${SYMFONY_VERSION})"
    composer require --no-update symfony/symfony="${SYMFONY_VERSION}"
    composer update --no-progress --no-interaction --prefer-dist
else
    echo "> Install dependencies through Composer"
    composer install --no-progress --no-interaction --prefer-dist
fi

if [ "$INSTALL" = "demoContentNonUniqueDB" ] ; then
  echo "> Install ezplatform demo-content"
  php ezpublish/console ezplatform:install --env=behat --no-debug demo
else
  echo "> Install ezplatform clean"
  php ezpublish/console ezplatform:install --env=behat --no-debug clean
fi

echo "> Run assetic dump for behat env"
php ezpublish/console --env=behat --no-debug assetic:dump
