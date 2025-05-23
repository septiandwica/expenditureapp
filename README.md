# Expenditure AP Application

A web-based expenditure management application built with Laravel, Filament, and Livewire.

## System Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL/SQLite
-   Web Server (Apache/Nginx)

## Key Features

-   Expenditure management (one of Cycle)
-   Admin panel with Filament
-   Modern interface with Livewire
-   Authentication and authorization system
-   User and role management
-   Reports and PDF export

## Installation

1. Clone the repository

```bash
git clone [REPOSITORY_URL]
cd expenditureap
```

2. Install PHP dependencies

```bash
composer install
```

3. Install JavaScript dependencies

```bash
npm install
```

4. Copy environment file

```bash
cp .env.example .env
```

5. Generate application key

```bash
php artisan key:generate
```

6. Configure database in `.env` file

7. Run database migrations

```bash
php artisan migrate
```

8. Run seeders (optional)

```bash
php artisan db:seed
```

9. Compile assets

```bash
npm run build
```

10. Start development server

```bash
php artisan serve
```

## Development

To run the application in development mode:

```bash
composer dev
```

This command will run:

-   Laravel server
-   Queue worker
-   Log watcher
-   Vite development server

## Testing

Run the test suite with:

```bash
composer test
```

## Technologies Used

-   [Laravel](https://laravel.com) - PHP Framework
-   [Filament](https://filamentphp.com) - Admin Panel
-   [Livewire](https://livewire.laravel.com) - Full-stack Framework
-   [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf) - PDF Generation
-   [Filament Shield](https://github.com/bezhansalleh/filament-shield) - Role & Permission Management

## License

This application is licensed under the [MIT License](LICENSE.md).
