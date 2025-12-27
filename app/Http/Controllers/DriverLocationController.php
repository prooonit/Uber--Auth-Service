<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DriverLocationController extends Controller
{
    //

    public function updateStatus(Request $request)
    {
        $payload = [
            'driver_id' => $request->input('driver_id'),
            'status' => $request->input('status'),
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('http://127.0.0.1:3000/driver/status', $payload);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to update driver status',
            ], 500);
        }

        return response()->json([
            'message' => 'Driver status updated successfully',
            'data' => $response->json()
        ]);
    }

    public function nearbyDrivers(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 5); 

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get(config('services.location_service.url').'/driver/nearby', [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
        ]);

        if(!$response->successful()) {
            return response()->json([
                'message' => 'Failed to fetch nearby drivers',
            ], 500);
        }
        return response()->json([
            'message' => 'Nearby drivers fetched successfully',
            'data' => $response->json()
        ]);
}
}
