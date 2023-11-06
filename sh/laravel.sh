#!/bin/bash
RED='\033[0;31m'
NC='\033[0m' # No Color
echo -e "${RED} Starting......"
export COMPOSER_ALLOW_SUPERUSER=1;
read -p "Project folder: " projectname
composer create-project laravel/laravel $projectname;
cd $projectname;
composer require laravel/jetstream
php artisan jetstream:install livewire
composer require nwidart/laravel-modules
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
composer require mhmiton/laravel-modules-livewire
php artisan vendor:publish --tag=modules-livewire-config
composer require hungnm28/livewire-admin
php artisan vendor:publish --tag=livewire-admin
yes|php "$( dirname -- "${BASH_SOURCE[0]}" )/artisan" la:set-composer
php "$( dirname -- "${BASH_SOURCE[0]}" )/artisan" la:create-admin-module
cd $projectname
npm run dev