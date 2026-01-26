<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\RentalRequestController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RoommateController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public property routes (accessible to both authenticated and unauthenticated users)
Route::middleware('auth.optional')->group(function () {
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::get('/properties/{id}/reviews', [ReviewController::class, 'getPropertyReviews']);

    // Search routes (public)
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('/search/advanced', [SearchController::class, 'advanced']);
    Route::get('/search/filters', [SearchController::class, 'filters']);

    // Featured and recent properties (public)
    Route::get('/properties/featured', [SearchController::class, 'featuredProperties']);
    Route::get('/properties/recent', [SearchController::class, 'recentProperties']);

    // Roommate search (public)
    Route::get('/roommates', [RoommateController::class, 'index']);
    Route::get('/roommates/{id}', [RoommateController::class, 'show']);
    Route::get('/roommates/filters', [RoommateController::class, 'filters']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/update', [AuthController::class, 'update']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/upload-avatar', [ProfileController::class, 'uploadAvatar']);
    });

    // Property routes (protected - require authentication)
    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store'])->middleware('check-role:landlord');
        Route::put('/{id}', [PropertyController::class, 'update'])->middleware('check-role:landlord');
        Route::delete('/{id}', [PropertyController::class, 'destroy'])->middleware('check-role:landlord');
        Route::post('/{id}/gallery', [PropertyController::class, 'uploadGallery'])->middleware('check-role:landlord');
        Route::delete('/gallery/{imageId}', [PropertyController::class, 'deleteGalleryImage'])->middleware('check-role:landlord');
        Route::get('/{id}/units', [PropertyController::class, 'getUnits']);
        Route::post('/{id}/units', [PropertyController::class, 'addUnit'])->middleware('check-role:landlord');
        Route::post('/{id}/favourite', [FavouriteController::class, 'store'])->middleware('check-role:tenant');
        Route::delete('/{id}/favourite', [FavouriteController::class, 'destroy'])->middleware('check-role:tenant');
        Route::post('/{id}/review', [ReviewController::class, 'createPropertyReview'])->middleware('check-role:tenant');
    });

    // Favourites
    Route::get('/favourites', [FavouriteController::class, 'index'])->middleware('check-role:tenant');

    // Rental Request routes
    Route::prefix('rental-requests')->group(function () {
        Route::get('/', [RentalRequestController::class, 'index']);
        Route::get('/{id}', [RentalRequestController::class, 'show']);
        Route::post('/', [RentalRequestController::class, 'store'])->middleware('check-role:tenant');
        Route::put('/{id}/approve', [RentalRequestController::class, 'approve'])->middleware('check-role:landlord');
        Route::put('/{id}/reject', [RentalRequestController::class, 'reject'])->middleware('check-role:landlord');
    });

    // Contract routes
    Route::prefix('contracts')->group(function () {
        Route::get('/', [ContractController::class, 'index']);
        Route::get('/{id}', [ContractController::class, 'show']);
        Route::post('/request/{requestId}/generate', [ContractController::class, 'generate'])->middleware('check-role:landlord');
        Route::put('/{id}', [ContractController::class, 'updateTerms'])->middleware('check-role:landlord');
        Route::post('/{id}/convert-to-rental', [ContractController::class, 'convertToRental'])->middleware('check-role:landlord');
        Route::post('/{id}/cancel', [ContractController::class, 'cancel'])->middleware('check-role:landlord');
        Route::get('/{id}/pdf', [ContractController::class, 'downloadPdf']);
        Route::post('/{id}/sign', [ContractController::class, 'sign']);
        Route::get('/{contractId}/verify', [ContractController::class, 'verify']);
        Route::get('/{contractId}/qr', [ContractController::class, 'getQrCode']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::post('/', [PaymentController::class, 'store'])->middleware('check-role:tenant');
        Route::put('/{id}/confirm', [PaymentController::class, 'confirm'])->middleware('check-role:landlord');
        Route::put('/{id}/reject', [PaymentController::class, 'reject'])->middleware('check-role:landlord');
        Route::get('/{id}/receipt', [PaymentController::class, 'downloadReceipt']);
    });

    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/conversations', [MessageController::class, 'conversations']);
        Route::get('/conversation/{userId}', [MessageController::class, 'getMessages']);
        Route::post('/{userId}', [MessageController::class, 'sendMessage']);
        Route::post('/{userId}/read', [MessageController::class, 'markAsRead']);
        Route::get('/unread-count', [MessageController::class, 'unreadCount']);
        Route::delete('/conversation/{userId}', [MessageController::class, 'deleteConversation']);
        Route::delete('/message/{messageId}', [MessageController::class, 'deleteMessage']);
        Route::post('/upload', [MessageController::class, 'upload']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // Support Ticket routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index']);
        Route::post('/', [SupportTicketController::class, 'store']);
        Route::get('/{id}', [SupportTicketController::class, 'show']);
        Route::post('/{id}/reply', [SupportTicketController::class, 'reply']);
        Route::put('/{id}/status', [SupportTicketController::class, 'updateStatus']);
    });

    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::post('/tenants/{tenantId}', [ReviewController::class, 'createTenantReview'])->middleware('check-role:landlord');
        Route::get('/tenants/{tenantId}', [ReviewController::class, 'getTenantReviews']);
    });

    // Wallet routes
    Route::prefix('wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'balance']);
        Route::post('/add-money', [WalletController::class, 'addMoney']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });

    // Block/Unblock User routes
    Route::prefix('blocked-users')->group(function () {
        Route::get('/', [BlockController::class, 'index']);
        Route::post('/', [BlockController::class, 'store']);
        Route::delete('/{userId}', [BlockController::class, 'destroy']);
        Route::get('/check/{userId}', [BlockController::class, 'check']);
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/my-reports', [ReportController::class, 'myReports']);
        Route::post('/property/{propertyId}', [ReportController::class, 'submitPropertyReport']);
        Route::post('/user', [ReportController::class, 'submitUserReport']);
    });

    // Roommate profile creation (if users can create their own)
    Route::post('/roommates', [RoommateController::class, 'store']);

    // Content routes (FAQ, About, Privacy, Terms, Contact, Settings)
    Route::prefix('content')->group(function () {
        Route::get('/faq', [ContentController::class, 'faq']);
        Route::get('/about', [ContentController::class, 'about']);
        Route::get('/privacy', [ContentController::class, 'privacy']);
        Route::get('/terms', [ContentController::class, 'terms']);
        Route::get('/contact', [ContentController::class, 'contact']);
        Route::get('/settings', [ContentController::class, 'settings']);
    });

    // Admin routes
    Route::middleware('check-role:admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/{id}', [AdminController::class, 'userDetails']);
        Route::put('/users/{id}/verify', [AdminController::class, 'verifyUser']);
        Route::put('/users/{id}/block', [AdminController::class, 'blockUser']);
        Route::get('/properties', [AdminController::class, 'properties']);
        Route::put('/properties/{id}/verify', [AdminController::class, 'verifyProperty']);
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::get('/reports/{type}/{id}', [AdminController::class, 'reportDetails']);
        Route::put('/reports/{type}/{id}/resolve', [AdminController::class, 'resolveReport']);
        Route::get('/activity-logs', [AdminController::class, 'activityLogs']);
    });
});
