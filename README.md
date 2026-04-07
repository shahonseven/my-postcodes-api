# Malaysia Postcodes API

A RESTful API service for Malaysian postcode lookup, validation, and search. Built with Laravel 13 and secured with token-based authentication.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)
![License](https://img.shields.io/badge/License-MIT-green)

## Features

- **2,932 unique postcodes** covering all Malaysian states and federal territories
- **443 cities/areas** across 16 states and territories
- Token-based API authentication using Laravel Sanctum
- RESTful endpoints for postcode lookup, search, and validation
- State and city listing with filtering capabilities

## Quick Start

### Prerequisites

- PHP 8.3 or higher
- Composer
- SQLite or MySQL

### Installation

```bash
# Clone the repository (include submodules for postcode data)
git clone --recurse-submodules https://github.com/shahonseven/my-postcodes-api.git
cd my-postcodes-api

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed postcode data
php artisan migrate:fresh --seed
```

### Development Server

```bash
# Start the development server
php artisan serve
```

API will be available at `http://localhost:8000`

## API Documentation

Full API documentation is available in **[API.md](API.md)**.

### Quick Reference

#### Authentication

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/register` | POST | Register new user |
| `/api/login` | POST | Login and get token |
| `/api/logout` | POST | Logout (revoke token) |
| `/api/user` | GET | Get current user |

#### Postcode Endpoints (Requires Bearer Token)

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/states` | GET | List all states |
| `/api/cities` | GET | List cities (filter by `state` or `search`) |
| `/api/postcode/{code}` | GET | Lookup postcode |
| `/api/search?q={query}` | GET | Search postcodes |

### Example Usage

```bash
# 1. Register a new account
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# 2. Login to get API token
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com", "password": "password123"}'

# 3. Use token to access postcode API
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/api/postcode/50000

# 4. Search for postcodes by city name
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost/api/search?q=Shah%20Alam"
```

## State Codes Reference

| Code | State/Territory | Code | State/Territory |
|------|-----------------|------|-----------------|
| JHR  | Johor           | PNG  | Pulau Pinang    |
| KDH  | Kedah           | SBH  | Sabah           |
| KTN  | Kelantan        | SGR  | Selangor        |
| MLK  | Melaka          | SRW  | Sarawak         |
| NSN  | Negeri Sembilan | TRG  | Terengganu      |
| PHG  | Pahang          | KUL  | WP Kuala Lumpur |
| PRK  | Perak           | LBN  | WP Labuan       |
| PLS  | Perlis          | PJY  | WP Putrajaya    |

## Running Tests

```bash
# Run all tests
php artisan test

# Run API tests only
php artisan test --filter=PostcodeApiTest
```

## Project Structure

```
my-postcodes-api/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php      # Authentication endpoints
│   │   └── PostcodeController.php  # Postcode API endpoints
│   └── Models/
│       ├── Postcode.php            # Postcode model
│       └── User.php                # User model with Sanctum
├── database/
│   ├── migrations/
│   │   ├── *_create_postcodes_table.php
│   │   └── *_create_personal_access_tokens_table.php
│   └── seeders/
│       └── PostcodeSeeder.php      # Seeds 2,932 postcodes
├── routes/
│   └── web.php                     # API route definitions
└── API.md                          # Full API documentation
```

## Data Source

Postcode data is sourced from the [malaysia-postcodes](https://github.com/heiswayi/malaysia-postcodes) repository, licensed under [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/deed.en).

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Built with ❤️ for Malaysian developers
