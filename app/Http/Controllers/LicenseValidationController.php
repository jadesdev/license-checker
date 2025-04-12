<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseValidationController extends Controller
{
    /**
     * Validate an access key
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'url' => 'required|string',
            'access_key' => 'required|string|max:64',
            'system_fingerprint' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:50',
            'timestamp' => 'nullable|date',
            'request_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid request: ' . $validator->errors()->first(),
            ], 400);
        }
    }
}
