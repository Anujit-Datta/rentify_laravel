# Rentify - House Rental REST API

A comprehensive REST API backend for a house/property rental application built with Laravel 11. The application supports three user roles (tenants, landlords, admins) and provides features for property listings, rental requests, contracts with digital signatures, payments, messaging, and more.

## Features

- **Role-Based Access Control** - Tenant, Landlord, and Admin roles with specific permissions
- **Property Management** - CRUD operations, image uploads, units, galleries, amenities
- **Rental Requests** - Tenant requests with landlord approval/rejection workflow
- **Digital Contracts** - PDF generation, RSA digital signatures, QR code verification
- **Payment System** - Rent payments, receipts, wallet integration
- **Messaging** - Real-time messaging between users
- **Reviews & Ratings** - Property and tenant reviews
- **Support Tickets** - Customer support system
- **Admin Panel** - User verification, property verification, report management

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Authentication:** Laravel Sanctum
- **Image Processing:** Intervention Image v3
- **QR Code Generation:** Simple Qrcode
- **Database:** MySQL (hosted remotely)

## Prerequisites

Before setting up this project, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.0
- **NPM** >= 9.0
- **Git**

### Check your versions

```bash
php --version    # Should be 8.2 or higher
composer --version
node --version   # Should be 18 or higher
npm --version
```

## Database Connection

This project connects to a remote MySQL database. The connection details are:

```
Host: mysql-206984-0.cloudclusters.net
Port: 10010
Database: house_renting
Username: admin
```

> **Note:** The database password is sensitive and should be obtained from the project environment variables or your team lead.

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd rentify_laravel
```

### 2. Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install Node.js dependencies:

```bash
npm install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit the `.env` file and configure the following:

```env
APP_NAME="Rentify"
APP_ENV=local
APP_KEY=                    # Will be generated in step 4
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (Remote MySQL)
DB_CONNECTION=mysql
DB_HOST=mysql-206984-0.cloudclusters.net
DB_PORT=10010
DB_DATABASE=house_renting
DB_USERNAME=admin
DB_PASSWORD=your_database_password_here

# Storage Configuration (using file-based for existing database)
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Storage Link

Create symbolic link for public file access (images, contracts, etc.):

```bash
php artisan storage:link
```

### 5. Cache Configuration

Clear and cache configuration:

```bash
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
```

### 6. Start Development Server

Option A - Run all services (server, queue, logs, vite):

```bash
composer run dev
```

Option B - Run individual services:

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Queue Worker (for background jobs)
php artisan queue:listen --tries=1

# Terminal 3 - Vite Dev Server (for frontend assets)
npm run dev
```

The API will be available at: `http://localhost:8000`

### 7. Testing the API

Import the Postman collection located at `Rentify-API.postman_collection.json` into Postman to test all endpoints.

**First Steps in Postman:**

1. Import `Rentify-API.postman_collection.json`
2. Update `baseUrl` variable if needed (default: `http://localhost:8000/api`)
3. Call `POST /register` to create a user
4. Copy the token from the login response
5. Set the `token` variable in Postman
6. All subsequent requests will use the token automatically

## Project Structure

```
rentify_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API Controllers
│   │   └── Middleware/         # Custom Middleware
│   └── Models/                 # Eloquent Models
├── config/                     # Configuration files
├── database/
│   └── seeders/               # Database seeders
├── public/                     # Public access point
├── resources/                  # Views and raw assets
├── routes/
│   ├── api.php                # API routes definition
│   └── web.php                # Web routes
├── storage/                    # App storage (files, logs)
├── tests/                      # Test files
├── .env                        # Environment configuration
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
├── CLAUDE.md                  # Claude Code guidance
└── Rentify-API.postman_collection.json  # Postman collection
```

## API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `POST /api/forgot-password` - Password reset request
- `POST /api/reset-password` - Reset password

### Properties
- `GET /api/properties` - List properties (with filters)
- `POST /api/properties` - Create property (Landlord only)
- `GET /api/properties/{id}` - Get property details
- `PUT /api/properties/{id}` - Update property (Landlord only)
- `DELETE /api/properties/{id}` - Delete property (Landlord only)

### Rental Requests
- `GET /api/rental-requests` - List requests
- `POST /api/rental-requests` - Create request (Tenant only)
- `PUT /api/rental-requests/{id}/approve` - Approve request (Landlord only)
- `PUT /api/rental-requests/{id}/reject` - Reject request (Landlord only)

### Contracts
- `GET /api/contracts` - List contracts
- `POST /api/contracts/request/{requestId}/generate` - Generate contract (Landlord only)
- `POST /api/contracts/{id}/sign` - Sign contract
- `GET /api/contracts/{id}/pdf` - Download contract PDF
- `GET /api/contracts/{contractId}/verify` - Verify contract signature

### Payments
- `GET /api/payments` - List payments
- `POST /api/payments` - Make payment (Tenant only)
- `PUT /api/payments/{id}/confirm` - Confirm payment (Landlord only)
- `GET /api/payments/{id}/receipt` - Download payment receipt

### Messaging
- `GET /api/messages/conversations` - Get conversations
- `POST /api/messages/{userId}` - Send message
- `POST /api/messages/{userId}/read` - Mark messages as read

### Admin
- `GET /api/admin/users` - List all users
- `PUT /api/admin/users/{id}/verify` - Verify user
- `GET /api/admin/reports` - List reports
- `PUT /api/admin/reports/{type}/{id}/resolve` - Resolve report

*See `Rentify-API.postman_collection.json` for complete list of 80+ endpoints.*

## User Roles

### Tenant
- Browse and search properties
- Add properties to favorites
- Send rental requests
- Make rent payments
- Write property reviews
- Message landlords

### Landlord
- Create and manage properties
- Receive and approve/reject rental requests
- Generate rental contracts
- Receive and confirm payments
- Write tenant reviews
- Message tenants

### Admin
- Verify users and properties
- Manage reported content
- View activity logs
- Block users
- Resolve disputes

## Common Issues & Solutions

### 1. "Class not found" errors

```bash
composer dump-autoload
php artisan optimize:clear
```

### 2. Storage files not accessible

```bash
php artisan storage:link
```

### 3. Database connection failed

- Verify your internet connection (remote database)
- Check database credentials in `.env`
- Ensure the database server is accessible

### 4. Permission denied errors

```bash
# Linux/Mac
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Windows (Git Bash)
chmod -R 775 storage bootstrap/cache
```

### 5. Port 8000 already in use

Use a different port:

```bash
php artisan serve --port=8080
```

Remember to update `APP_URL` in `.env` and `baseUrl` in Postman.

### 6. "Table 'house_renting.sessions' doesn't exist" error

This project uses file-based sessions, not database sessions. Ensure your `.env` has:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 7. "Column not found: 1054 Unknown column 'updated_at'" error

The existing database schema doesn't include Laravel's default timestamp columns. These have been disabled in the User model and other models as needed. If you encounter this with other tables, add `public $timestamps = false;` to the respective model.

## Development Workflow

### Running Tests

```bash
php artisan test
```

### Code Formatting

```bash
./vendor/bin/pint
```

### Clear Caches

```bash
php artisan optimize:clear
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

## Production Deployment

When deploying to production:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Generate an optimized production build: `npm run build`
3. Optimize the application: `php artisan optimize`
4. Set up queue workers with a process manager like Supervisor
5. Configure proper cron jobs for Laravel scheduler
6. Ensure proper file permissions on storage directories
7. Set up SSL/HTTPS
8. Configure CORS for allowed origins

## Security Notes

- Never commit `.env` file to version control
- Use strong passwords for database connection
- Keep `APP_DEBUG=false` in production
- Regularly update dependencies: `composer update` and `npm update`
- Use HTTPS in production
- Implement rate limiting for API endpoints
- Validate and sanitize all user inputs

## Support

For issues, questions, or contributions:
- Check `CLAUDE.md` for development guidance
- Review `IMPLEMENTATION_PROGRESS.md` for project status
- Open an issue in the repository

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
