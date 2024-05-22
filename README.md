PHP ^8.2, Laravel 11

## Setup the env

```
cp .env.example .env
```

## Install dependencies

```
composer install
```

## Run sail

```
sail up
```

or

```
vendor/bin/sail up
```

## Link storage

```
sail artisan storage:link
```
