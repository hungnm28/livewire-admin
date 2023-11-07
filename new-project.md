# Create new project use livewire-admin
## Install all step
```sh
  curl -o laravel.sh https://github.com/hungnm28/livewire-admin/blob/main/sh/laravel.sh && chmod 755 laravel.sh && ./laravel.sh
```

## 1. Create new Laravel project
Laravel document: [Laravel document](https://laravel.com/docs/10.x).

```sh
    composer create-project laravel/laravel example-app
    cd example-app
```
## 2. Install jetstream  use livewire
Jetstram document: [Jetstram document](https://jetstream.laravel.com/3.x/introduction.html).
```sh
     composer require laravel/jetstream
```
```sh
    php artisan jetstream:install livewire
```
## 3. Install nwidart/laravel-modules
Laravel-module document: [Laravel-module document](https://docs.laravelmodules.com/v9/installation-and-setup).
```sh
    composer require nwidart/laravel-modules
```
```sh
    php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
```
### Add Autoloading
By default the module classes are not loaded automatically.
You can autoload your modules using psr-4 on composer.json file. For example :
```json
    {
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "Modules/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
    }
  }
}
```
### run autoload
```shell
   composer dump-autoload
```

## 4. Install mhmiton/laravel-modules-livewire
document: [ mhmiton/laravel-modules-livewire](https://github.com/mhmiton/laravel-modules-livewire).

```sh 
    composer require mhmiton/laravel-modules-livewire
```
```sh 
    php artisan vendor:publish --tag=modules-livewire-config
```

## 5. Install livewire-admin
```sh
composer require hungnm28/livewire-admin
```
### publish folder
```shell
php artisan vendor:publish --tag=livewire-admin --force
php artisan vendor:publish --tag=livewire-admin-vite --force
php artisan vendor:publish --tag=livewire-admin-permission --force
```
