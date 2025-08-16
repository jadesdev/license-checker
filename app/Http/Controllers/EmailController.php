<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    private $allowedDomains = [
        'jadesdev.com.ng',
        'www.jadesdev.com.ng',
        'localhost'
    ];

    private $requiredApiKey = 'jK9mPq4sVvXy2zA8bB3cD5eF6gH7jK9mPq4sVvXy2zA8bB3cD5eF6gH7jK9mPq4sVvXy2';

    /**
     * Send contact form email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request)
    {
        $referer = parse_url($request->header('referer'), PHP_URL_HOST) ?? '';

        $isAllowedDomain = in_array($referer, $this->allowedDomains);
        $hasValidApiKey = $request->header('X-API-KEY') == $this->requiredApiKey;
        if (!$hasValidApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }

        if (!$isAllowedDomain) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Domain not allowed.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:100',
            'budget' => 'nullable|string|max:50',
            'timeline' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            Mail::to(env('MAIL_TO_ADDRESS', 'jadesdevelopers@gmail.com'))
                ->send(new ContactFormMail($request->all()));

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send contact form email: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again later.'
            ], 500);
        }
    }
}
