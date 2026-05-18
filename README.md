# Faculty Elevate

Faculty Elevate is a Laravel MVC platform for smart faculty capacity building and performance assessment.

## Implemented Modules

- Role-based route groups for `admin`, `hod`, and `faculty`
- MongoDB model layer (`MongoDB\Laravel\Eloquent\Model`) with explicit collections
- Performance Index service and weight configuration flow
- Recommendation engine service (rule-based suggestions)
- Gamification service (XP + level + badge notification)
- Annual PDF report generation service (DOMPDF)
- Faculty wellbeing survey and burnout index capture
- Blade screens for admin, HOD, faculty dashboards, goals, leaderboard, profile, and workshops

## Tech Stack

- PHP 8.2+
- Laravel 12
- MongoDB package: `mongodb/laravel-mongodb`
- Auth scaffolding: Laravel Breeze (Blade)
- PDF: `barryvdh/laravel-dompdf`
- Frontend: Blade + Tailwind + Vite

## Prerequisites

1. PHP 8.2+
2. Composer
3. Node.js + npm
4. MongoDB server running locally (`127.0.0.1:27017`)
5. **MongoDB PHP extension enabled** (`ext-mongodb`)

### Enable MongoDB PHP extension (Windows/XAMPP)

1. Download the matching `php_mongodb.dll` for your PHP version/thread-safety.
2. Place it in `C:\xampp\php\ext`.
3. Add this line in `C:\xampp\php\php.ini`:

```ini
extension=php_mongodb.dll
```

4. Restart terminal/web server.
5. Verify:

```bash
php -m
```

You should see `mongodb` in the module list.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Update `.env` values:

```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=faculty_elevate
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@facultyelevate.com"
MAIL_FROM_NAME="Faculty Elevate"
```

## Run the Project

### Development mode (recommended)

```bash
composer run dev
```

This starts:
- Laravel app server
- Queue listener
- Log watcher
- Vite dev server

### Manual run

```bash
php artisan serve
npm run dev
```

## Testing

```bash
php artisan test
```

### Role access smoke test

```bash
php artisan test --filter=RoleDashboardAccessTest
```

### Important test note

If `ext-mongodb` is not enabled, tests that touch MongoDB models will fail with errors like:

- `Class "MongoDB\BSON\UTCDateTime" not found`
- `SQLiteConnection::getCollection does not exist`

Enable `ext-mongodb` first, then rerun tests.

## Key Routes

- Admin: `/admin/dashboard`
- HOD: `/hod/dashboard`
- Faculty: `/faculty/dashboard`

Authentication routes are provided by Breeze under `/login` and `/register`.

## Production Readiness Checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Use a real app URL in `APP_URL` and enable HTTPS at the web server/proxy.
- Configure secure session cookie settings:
  - `SESSION_SECURE_COOKIE=true`
  - `SESSION_HTTP_ONLY=true`
  - `SESSION_SAME_SITE=lax` (or `strict` if your flow allows it)
- Use an SMTP provider for notifications (`MAIL_MAILER=smtp`) and set valid credentials.
- Run queue workers in production (`php artisan queue:work`) because badge and burnout notifications rely on notification delivery.
- Ensure MongoDB is available and indexed (indexes are created in `AppServiceProvider`).
- Seed initial platform data with:

```bash
php artisan db:seed
```
