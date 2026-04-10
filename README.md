# Care Flow System
# CareFlow

CareFlow is an Office File Registry and Routing System designed to manage physical and digital file movement across departments.

## Features

* File registration (existing or temporary file number)
* Department-based routing (HOD controlled)
* File movement tracking and audit trail
* Document scanning and attachment
* Role-based access control (Registry, Staff, HOD, Admin)

## Tech Stack

* Backend: Laravel
* Frontend: Vue 3 + Tailwind
* Database: PostgreSQL / MySQL
* Auth & Permissions: Spatie Laravel Permission

## Installation

```bash
git clone https://github.com/your-username/care-flow-system.git
cd care-flow-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

## Usage

1. Registry registers incoming file
2. System assigns temporary or existing file number
3. File is routed to HOD
4. HOD assigns or routes file
5. Staff processes and returns to HOD

## License

MIT
