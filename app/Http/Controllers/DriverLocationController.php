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

    public function estimateFare(Request $request)
    {
        $pickupLat = $request->input('pickup_lat');
        $pickupLng = $request->input('pickup_lng');
        $dropoffLat = $request->input('dropoffLat');
        $dropoffLng = $request->input('dropoffLng');

        $distance = $this->haversine($pickupLat,$pickupLng,$dropoffLat,$dropoffLng);

        $baseFare = 2.50;
        $costPerKm = 1.20;
        $estimatedFare = $baseFare + ($costPerKm * $distance);
        return response()->json([
            'message' => 'Fare estimated successfully',
            'data' => [
                'estimated_fare' => round($estimatedFare, 2),
                'distance_km' => round($distance, 2)
            ]
        ]);

    }
    public function haversine($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; 

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance; 
    }
}
