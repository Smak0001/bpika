<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Measurement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeasurementController extends Controller
{
    /**
     * Checks request for validity and stores the input data if it is.
     *
     * Returns a JSON message with outcome of validation.
     *
     * @param Request $request
     * @return false|Response|string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, Measurement::rules());

        if ($this->requestCameBeforeTimeout($validated)) {
            return new Response(['error' => 'Already added measurement less than 10 minutes ago.'],Response::HTTP_PRECONDITION_FAILED);
        }

        $measurement = Measurement::create($validated);

        return json_encode(['message' => 'Measurement created with id ' . $measurement->id]);

    }

    /**
     * @param array $validated
     * @return bool
     */
    private function requestCameBeforeTimeout(array $validated): bool
    {
        $lastMeasurement = Measurement::getLastMeasurementByStationName($validated['station'])->created_at;
        $requestTimeout = Carbon::now()->addMinutes(env('REQUEST_TIMEOUT_IN_MINUTES'));

        if ($lastMeasurement < $requestTimeout) {
            return true;
        }

        return false;
    }
}
