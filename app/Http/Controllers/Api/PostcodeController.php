<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Postcode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostcodeController extends Controller
{
    /**
     * List all states with their codes.
     */
    public function states(): JsonResponse
    {
        $states = Postcode::select('state', 'state_code')
            ->distinct()
            ->orderBy('state')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->state,
                'code' => $item->state_code,
            ]);

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }

    /**
     * List all cities, optionally filtered by state.
     */
    public function cities(Request $request): JsonResponse
    {
        $query = Postcode::select('city', 'state', 'state_code')
            ->distinct();

        if ($request->has('state')) {
            $query->where('state_code', strtoupper($request->state));
        }

        if ($request->has('search')) {
            $query->where('city', 'like', '%'.$request->search.'%');
        }

        $cities = $query->orderBy('city')->get();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * Lookup postcode information by postcode.
     */
    public function lookup(string $postcode): JsonResponse
    {
        $results = Postcode::where('postcode', $postcode)->get();

        if ($results->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Postcode not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $results->map(fn ($item) => [
                'postcode' => $item->postcode,
                'city' => $item->city,
                'state' => $item->state,
                'state_code' => $item->state_code,
            ]),
        ]);
    }

    /**
     * Search by city name or postcode.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('q');

        if (! $search) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
            ], 400);
        }

        $results = Postcode::query()
            ->where('city', 'like', "%{$search}%")
            ->orWhere('state', 'like', "%{$search}%")
            ->orWhere('postcode', 'like', "{$search}%")
            ->orderBy('city')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'total' => $results->count(),
                'limit' => 50,
            ],
        ]);
    }
}
