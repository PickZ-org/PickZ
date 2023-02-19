![PickZ logo](/public/img/logo_small.png)

PickZ is an open-source warehouse management system built for small to medium-sized operations.

## Table of contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Built With](#built-with)
* [License](#license)
* [Versioning](#versioning)
* [Coding style](#coding-style)


## Requirements

* PHP ^8.1
* Composer ^2.2.0

## Installation

Clone or extract files into a folder and run:

```s
composer install -o --no-dev
```

There is a .env.example which is a template of the .env file that the project expects us to have. 
So we will make a copy of the .env.example file and create a .env file that we can start to fill out to do things like database configuration in the next few steps.

```
cp .env.example .env
```

Now change the variables in the .env file for your database, timezone, and app URL.

###### Application settings
`APP_TIMEZONE` Application timezone \
`APP_URL` Application URL

###### Database settings
`DB_CONNECTION` Database type \
`DB_HOST` IP or hostname \
`DB_PORT` Port \
`DB_DATABASE` Database name \
`DB_USERNAME` Username \
`DB_PASSWORD` Password

Run these commands to generate an app encryption key and set up the initial database tables and records.

```
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

PickZ should now be ready to run!

## Built With

This project wouldn't exist without the helps of:

* [Laravel](https://laravel.com/)
* [AdminLTE](https://adminlte.io/)
* [Bootstrap](https://getbootstrap.com/)
* [DataTables](https://datatables.net/)

## License

PickZ is distributed under the [AGPLv3](https://www.gnu.org/licenses/agpl-3.0.en.html) license. \
Copyright (c) 2023 PickZ contributors. All rights reserved

## Versioning

This project adheres to [Semantic versioning](http://semver.org/). 

## Coding style

We use [PSR-12](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md) coding standard.



