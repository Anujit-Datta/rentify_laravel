<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Models\UserReport;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct()
    {
        // All admin routes require authentication
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all users.
     */
    public function users(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by verification
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        // Log activity
        AdminActivityLog::create([
            'admin_id' => $user->id,
            'admin_name' => $user->name,
            'action' => 'view_users',
            'description' => 'Viewed all users',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    /**
     * Get user details.
     */
    public function userDetails($id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $user = User::with(['tenant', 'landlord'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Verify/unverify landlord.
     */
    public function verifyUser(Request $request, $id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can be verified'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'is_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'is_landlord_verified' => $request->is_verified
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => $adminUser->id,
            'admin_name' => $adminUser->name,
            'action' => $request->is_verified ? 'verify_landlord' : 'unverify_landlord',
            'description' => ($request->is_verified ? 'Verified' : 'Unverified') . " landlord: {$user->name}",
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->is_verified ? 'Landlord verified successfully' : 'Landlord verification removed',
            'data' => $user
        ]);
    }

    /**
     * Block/unblock user.
     */
    public function blockUser(Request $request, $id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_blocked' => 'required|boolean',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'status' => $request->is_blocked ? 'blocked' : 'active'
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => $adminUser->id,
            'admin_name' => $adminUser->name,
            'action' => $request->is_blocked ? 'block_user' : 'unblock_user',
            'description' => ($request->is_blocked ? 'Blocked' : 'Unblocked') . " user: {$user->name}" . ($request->reason ? " - Reason: {$request->reason}" : ''),
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->is_blocked ? 'User blocked successfully' : 'User unblocked successfully',
            'data' => $user
        ]);
    }

    /**
     * Get all properties.
     */
    public function properties(Request $request)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = Property::with('landlord');

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('available', $request->boolean('available'));
        }

        $properties = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'pagination' => [
                'total' => $properties->total(),
                'per_page' => $properties->perPage(),
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
            ]
        ]);
    }

    /**
     * Verify property.
     */
    public function verifyProperty(Request $request, $id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $property->update(['is_verified' => $request->is_verified]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => $adminUser->id,
            'admin_name' => $adminUser->name,
            'action' => $request->is_verified ? 'verify_property' : 'unverify_property',
            'description' => ($request->is_verified ? 'Verified' : 'Unverified') . " property: {$property->property_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->is_verified ? 'Property verified successfully' : 'Property verification removed',
            'data' => $property
        ]);
    }

    /**
     * Get all reports.
     */
    public function reports(Request $request)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        // Get property reports
        $propertyReports = PropertyReport::with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user reports
        $userReports = UserReport::with(['reporter', 'reportedUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'property_reports' => $propertyReports,
                'user_reports' => $userReports,
                'total_reports' => $propertyReports->count() + $userReports->count(),
            ]
        ]);
    }

    /**
     * Get report details.
     */
    public function reportDetails($type, $id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        if ($type === 'property') {
            $report = PropertyReport::with(['property', 'user'])->find($id);
        } elseif ($type === 'user') {
            $report = UserReport::with(['reporter', 'reportedUser'])->find($id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report type'
            ], 400);
        }

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Resolve report.
     */
    public function resolveReport(Request $request, $type, $id)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($type === 'property') {
            $report = PropertyReport::find($id);
        } elseif ($type === 'user') {
            $report = UserReport::find($id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report type'
            ], 400);
        }

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }

        $report->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
            'resolved_by' => $adminUser->id,
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => $adminUser->id,
            'admin_name' => $adminUser->name,
            'action' => 'resolve_report',
            'description' => "Resolved {$type} report ID: {$id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully',
            'data' => $report
        ]);
    }

    /**
     * Get activity logs.
     */
    public function activityLogs(Request $request)
    {
        $adminUser = Auth::user();

        if (!$adminUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = AdminActivityLog::query();

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'pagination' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
            ]
        ]);
    }
}
