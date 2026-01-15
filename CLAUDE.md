# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Rentify is a comprehensive REST API backend for a house/property rental application built with Laravel 11. The application supports three user roles (tenants, landlords, admins) and provides features for property listings, rental requests, contracts with digital signatures, payments, messaging, and more.

## Common Commands

### Development
```bash
# Start development server (includes queues and vite)
composer run dev

# Start only the Laravel server
php artisan serve

# Run queue worker
php artisan queue:listen --tries=1

# Watch for frontend changes
npm run dev
```

### Build & Assets
```bash
# Build for production
npm run build

# Compile assets
vite build
```

### Testing & Quality
```bash
# Run tests
php artisan test

# Run Pint (Laravel Prettier)
./vendor/bin/pint

# Run specific test
php artisan test --filter TestClassName
```

### Database
```bash
# Note: This project uses an existing database schema (migrations are not used)
# Database connection is configured in .env:
# - DB_CONNECTION=mysql
# - DB_HOST=mysql-206984-0.cloudclusters.net:10010
# - DB_DATABASE=house_renting
```

### Cache & Config
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Create storage link for file uploads
php artisan storage:link
```

## Architecture

### Role-Based Access Control

The application has three user roles stored in the `users.role` field:
- **tenant** - Can browse properties, send rental requests, make payments, write reviews
- **landlord** - Can manage properties, approve/reject requests, generate contracts, receive payments
- **admin** - Can verify users/properties, manage reports, view activity logs

All authenticated routes use Laravel Sanctum tokens. Role checking is done via `check-role` middleware:
```php
Route::post('/properties', [PropertyController::class, 'store'])->middleware('check-role:landlord');
```

### Database Schema (No Migrations)

This project connects to an existing `house_renting` database. The schema was pre-existing and models map to existing tables. Key relationships:

- `users` hasOne: `tenant`, `landlord`, `admin`
- `properties` belongsTo: `users` (landlord)
- `properties` hasMany: `property_units`, `property_floors`, `property_gallery`, `property_amenities`
- `contracts` belongsTo: `users` (tenant & landlord), `properties`
- `rental_requests` → `contracts` → `rentals` → `rent_payments`

### Controllers

All API controllers are in `app/Http/Controllers/Api/`:
- `AuthController` - Registration, login, password reset, profile updates
- `PropertyController` - CRUD, image uploads, units, search/filter
- `RentalRequestController` - Tenant requests and landlord approval/rejection
- `ContractController` - PDF generation, digital signatures, QR codes, verification
- `PaymentController` - Payment processing, receipts, confirmation
- `MessageController` - Real-time messaging conversations
- `NotificationController` - User notifications management
- `SupportTicketController` - Customer support tickets
- `ReviewController` - Property reviews and tenant reviews
- `WalletController` - Digital wallet balance and transactions
- `ProfileController` - User profile management
- `FavouriteController` - Tenant favorites
- `AdminController` - Admin management endpoints

### Middleware

Custom middleware registered in `bootstrap/app.php`:
- `check-role` - Validates user role (tenant/landlord/admin)
- `check-ownership` - Ensures user owns the resource being modified

### Key Dependencies

- `laravel/sanctum` - API token authentication
- `intervention/image` - Image processing for property photos
- `simplesoftwareio/simple-qrcode` - QR code generation for contract verification
- `laravel/framework` ^11.31

### File Uploads

Property images are uploaded to `storage/app/public/properties/`. Remember to run `php artisan storage:link` to make files accessible via `public/storage`.

### Contract System

Contracts use a digital signature workflow:
1. Landlord generates contract from approved rental request
2. Both parties sign digitally (RSA keys stored in `users.public_key` and `users.private_key`)
3. QR code generated for verification
4. PDF stored in `storage/app/public/contracts/`

### API Routes

All routes are in `routes/api.php` with the prefix `/api`. Public routes: register, login, forgot-password, reset-password. All other routes require `auth:sanctum` middleware.

## Important Notes

- The database uses an existing schema - do not create migrations
- All passwords use bcrypt hashing
- RSA key pairs are stored in the database for digital signatures
- Image file paths are preserved from the legacy system
- Controllers return API Resources for consistent JSON responses
- Role checks must be done at the controller/middleware level (not just frontend)
