<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roommate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoommateController extends Controller
{
    /**
     * Search for roommates with filters.
     */
    public function index(Request $request)
    {
        $query = Roommate::query();

        // Filter by location
        if ($request->has('location') && !empty($request->location)) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        // Filter by budget (max budget)
        if ($request->has('budget') && !empty($request->budget)) {
            $query->where('budget', '<=', $request->budget);
        }

        // Filter by gender
        if ($request->has('gender') && !empty($request->gender)) {
            $query->where('gender', $request->gender);
        }

        // Filter by smoking preference
        if ($request->has('smoking') && !empty($request->smoking)) {
            $query->where('smoking', $request->smoking);
        }

        // Filter by pets preference
        if ($request->has('pets') && !empty($request->pets)) {
            $query->where('pets', $request->pets);
        }

        // Filter by cleanliness
        if ($request->has('cleanliness') && !empty($request->cleanliness)) {
            $query->where('cleanliness', $request->cleanliness);
        }

        // Filter by occupation
        if ($request->has('occupation') && !empty($request->occupation)) {
            $query->where('occupation', 'LIKE', '%' . $request->occupation . '%');
        }

        // Get results
        $roommates = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $roommates,
            'count' => $roommates->count()
        ]);
    }

    /**
     * Get a specific roommate profile.
     */
    public function show($id)
    {
        $roommate = Roommate::find($id);

        if (!$roommate) {
            return response()->json([
                'success' => false,
                'message' => 'Roommate profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $roommate
        ]);
    }

    /**
     * Create a new roommate profile (if users can create their own).
     */
    public function store(Request $request)
    {
        // Note: This endpoint can be enabled if you want users to create roommate profiles
        // For now, it's commented out as the PHP version seems to have a static list

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'required|integer|min:18|max:100',
            'location' => 'required|string|max:255',
            'budget' => 'required|integer|min:0',
            'occupation' => 'nullable|string|max:100',
            'smoking' => 'required|in:Yes,No',
            'pets' => 'required|in:Yes,No',
            'cleanliness' => 'required|in:High,Medium,Low',
            'about' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $roommate = Roommate::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'location' => $request->location,
            'budget' => $request->budget,
            'occupation' => $request->occupation,
            'smoking' => $request->smoking,
            'pets' => $request->pets,
            'cleanliness' => $request->cleanliness,
            'about' => $request->about,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Roommate profile created successfully',
            'data' => $roommate
        ], 201);
    }

    /**
     * Get search filters/options for roommates.
     */
    public function filters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'genders' => ['Male', 'Female', 'Other'],
                'smoking_options' => ['Yes', 'No'],
                'pets_options' => ['Yes', 'No'],
                'cleanliness_options' => ['High', 'Medium', 'Low'],
            ]
        ]);
    }
}
