<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyReport;
use App\Models\UserReport;
use App\Models\ActionLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Submit a property report.
     */
    public function submitPropertyReport(Request $request, $propertyId)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'report_reason' => 'required|string|max:100',
            'report_description' => 'required|string|max:1000',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if property exists
        $property = \App\Models\Property::find($propertyId);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Handle screenshots - if array of base64 or file paths
        $screenshots = [];
        if ($request->has('screenshots')) {
            $screenshots = $request->screenshots;
        }

        $report = PropertyReport::create([
            'property_id' => $propertyId,
            'user_id' => $user->id,
            'report_reason' => $request->report_reason,
            'report_description' => $request->report_description,
            'screenshots' => json_encode($screenshots),
            'status' => 'pending',
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => "New Property Report #{$report->id}",
                'message' => "Property '{$property->property_name}' was reported for: {$request->report_reason}",
                'type' => 'report',
                'related_id' => $report->id,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property report submitted successfully',
            'data' => $report
        ], 201);
    }

    /**
     * Submit a user report.
     */
    public function submitUserReport(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'reported_user_id' => 'required|integer|exists:users,id',
            'reason' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'screenshot' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $reportedUserId = $request->reported_user_id;

        // Check if trying to report self
        if ($reportedUserId == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot report yourself'
            ], 400);
        }

        // Handle screenshot upload
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $filename = 'report_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('reports', $filename, 'public');
            $screenshotPath = 'storage/' . $path;
        }

        $report = UserReport::create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUserId,
            'reason' => $request->reason,
            'description' => $request->description,
            'screenshot_path' => $screenshotPath,
            'status' => 'pending',
        ]);

        // Log the action
        ActionLog::create([
            'user_id' => $user->id,
            'action_type' => 'report_user',
            'target_user_id' => $reportedUserId,
            'description' => "Reason: {$request->reason} - {$request->description}",
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $reportedUser = User::find($reportedUserId);
            Notification::create([
                'user_id' => $admin->id,
                'title' => "New User Report #{$report->id}",
                'message' => "{$user->name} reported {$reportedUser->name} for: {$request->reason}",
                'type' => 'report',
                'related_id' => $report->id,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User report submitted successfully. Admin will review it soon.',
            'data' => $report
        ], 201);
    }

    /**
     * Get user's submitted reports.
     */
    public function myReports(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'all');

        if ($type === 'property') {
            $reports = PropertyReport::where('user_id', $user->id)
                ->with('property')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($type === 'user') {
            $reports = UserReport::where('reporter_id', $user->id)
                ->with(['reporter', 'reportedUser'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $propertyReports = PropertyReport::where('user_id', $user->id)
                ->with('property')
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'type' => 'property',
                        'reason' => $report->report_reason,
                        'description' => $report->report_description,
                        'status' => $report->status,
                        'created_at' => $report->created_at,
                        'property' => $report->property,
                    ];
                });

            $userReports = UserReport::where('reporter_id', $user->id)
                ->with(['reporter', 'reportedUser'])
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'type' => 'user',
                        'reason' => $report->reason,
                        'description' => $report->description,
                        'status' => $report->status,
                        'created_at' => $report->created_at,
                        'reported_user' => $report->reportedUser,
                    ];
                });

            $reports = collect([...$propertyReports, ...$userReports])->sortByDesc('created_at')->values();
        }

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}
