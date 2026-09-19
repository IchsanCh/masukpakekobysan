# masukpakeko

Web-based mail management system for handling incoming and outgoing mail, dispositions, retention, and digital archives.

Built with Laravel, Tailwind CSS, daisyUI, and Fonnte.

## Features

- Incoming mail management
- Outgoing mail management
- Mail disposition
- Employee notifications
- WhatsApp notifications via Fonnte
- Mail retention period
- Digital archiving
- Mail history and tracking

## Tech Stack

- Laravel
- PHP
- Blade
- Tailwind CSS
- daisyUI
- MySQL
- Fonnte
- Vite

## Mail Flow

```text
Incoming Mail
      │
      ▼
   Disposition
      │
      ▼
    Employee
      │
      ▼
   Follow-up
      │
      ▼
    Archive
      │
      ▼
   Retention
```

## Installation

Clone the repository:
```bash
git clone https://github.com/USERNAME/masukpakeko.git
cd masukpakeko
```

Install dependencies:
```bash
composer install
npm install
```

Create the environment file:
```bash
cp .env.example .env
php artisan key:generate
```
Configure the database and Fonnte credentials in .env, then run:
```bash
php artisan migrate
npm run build
```
Start the development server:
```bash
php artisan serve
```
For frontend development:
```bash
npm run dev
```
