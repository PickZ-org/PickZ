<h1 align="center">
    <img src="./.github/assets/logo_small.png" alt="PickZ Logo">
</h1>

<p align="center">
    <i>PickZ is an open-source warehouse management system designed to streamline warehouse operations in small to medium-sized environments.</i>
</p>

<p align="center">
  <a href="https://www.pickz.org" target="_blank">Website</a> |
  <a href="https://docs.pickz.org" target="_blank">Documentation</a> |
  <a href="https://demo.pickz.org" target="_blank">Demo</a> |
</p>

<h4 align="center">
    <img alt="GitHub License" src="https://img.shields.io/github/license/PickZ-org/PickZ" />
    <img alt="GitHub Release" src="https://img.shields.io/github/v/release/PickZ-org/PickZ" />
</h4>

<p align="center">
<img src="./.github/assets/pickz_preview.jpg" alt="PickZ Preview">
</p>

## Features

📦 **Smart inventory management:** Track inventory in real-time.

📄 **Seamless order handling:** Manage inbound and outbound orders efficiently.

🧭️ **Guided putaway:** Follow guided instructions for optimal storage.

📲 **Handheld scanner support:** Utilize handheld scanners for quick, accurate tasks.

📅 **FEFO picking:** Implement First Expired, First Out (FEFO) picking method.

🧐 **Dynamic stock grouping:** Organize stock flexibly based on your needs.

🔖 **Task and location priorities:** Optimize operations with task and location prioritization.

❌ **Cross-docking:** Improve efficiency with cross-docking capabilities.

💵 **Invoicing:** Generate invoices based on quantities and storage duration.

👥 **User & role management:** Control user access and permissions.

## Requirements

* PHP ^8.2
* Composer ^2.6

Alternatively, you can use [Docker](https://www.docker.com/) to run the application.

## Installation

Clone or extract the files into a folder and run:

```sh
composer install -o --no-dev
```

Copy the `.env.example` file to `.env` and configure your environment settings:

```sh
cp .env.example .env
```

Update the `.env` file with your database settings, timezone, and application URL.

### Application settings

- `APP_TIMEZONE`: Application timezone
- `APP_URL`: Application URL

### Database settings

- `DB_CONNECTION`: Database type
- `DB_HOST`: IP or hostname
- `DB_PORT`: Port
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Username
- `DB_PASSWORD`: Password

Run the following commands to generate an encryption key and set up the database:

```sh
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

Finally, add a cron job for scheduled tasks:

```sh
* * * * * cd /path-to-PickZ && php artisan schedule:run >> /dev/null 2>&1
```

PickZ should now be ready to use!

**Default Credentials:**
- Username: `admin`
- Password: `admin`

Make sure to change the default password after logging in.

## Docker Installation

To install and run PickZ using Docker, follow these steps:

1. **Clone the repository:**

    ```sh
    git clone https://github.com/PickZ-org/PickZ.git
    cd PickZ
    ```

2. **Copy the `.env.example` file to `.env` and update the environment variables as needed:**

    ```sh
    cp .env.example .env
    ```

3. **Build and start the Docker containers:**

    ```sh
    docker-compose up --build -d
    ```

4. **Install PHP dependencies using Composer inside the Docker container:**

    ```sh
    docker-compose exec app composer install -o --no-dev
    ```

5. **Generate the application key:**

    ```sh
    docker-compose exec app php artisan key:generate
    ```

6. **Run database migrations and seeders:**

    ```sh
    docker-compose exec app php artisan migrate --force
    docker-compose exec app php artisan db:seed --force
    ```

PickZ should now be ready to use!

**Default Credentials:**
- Username: `admin`
- Password: `admin`

Make sure to change the default password after logging in.

## Built With

This project wouldn't be possible without:

* [Laravel](https://laravel.com/)
* [AdminLTE](https://adminlte.io/)
* [Bootstrap](https://getbootstrap.com/)
* [DataTables](https://datatables.net/)

## Contributing

Contributions are always welcome!

See [Contributing](CONTRIBUTING.md) for guidelines on how to contribute.

Please adhere to this project's [Code of Conduct](CODE_OF_CONDUCT.md).

## License

PickZ is distributed under the [AGPLv3](https://www.gnu.org/licenses/agpl-3.0.en.html) license.

