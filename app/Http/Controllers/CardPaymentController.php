<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CardPaymentController extends Controller
{
    public function initPayment(Request $request)
    {
        Log::info($request->all());
        $client = new Client();
        $url = env('PAYCENTER_ENDPOINT') . '/payment-init'; // The payment initialization endpoint
        
        // Payment initialization data
        $paymentData = [
            'clientId' => env('PAYCENTER_CLIENT_ID'),
            'type' => 'PURCHASE',
            'amount' => [
                'paymentAmount' => $request->amount, // Replace with actual amount
                'currency' => 'LKR'
            ],
            'redirect' => [
                'returnUrl' => route('payment.complete'), // URL for handling payment completion
                'returnMethod' => 'GET'
            ],
            'tokenize' => false,
            'clientRef' => $request->payment_id, // Optional: Order reference
        ];

        try {
            // Make the payment initialization request
            $response = $client->post($url, [
                'json' => $paymentData,
                'headers' => [
                    'Authorization' => 'Bearer ' . env('PAYCENTER_AUTH_TOKEN'),
                    'HMAC' => env('PAYCENTER_HMAC_SECRET'),
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);
            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $responseBody['paymentPageUrl']]);

        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Payment initialization failed', 'error' => $e->getMessage()]);

        }
    }

    // Handle Payment Completion
    public function completePayment(Request $request)
    {
        $reqId = $request->input('reqid'); // Get reqid from the return URL
        
        $client = new Client();
        $url = env('PAYCENTER_ENDPOINT') . '/payment-complete'; // The payment completion endpoint
        
        $completeData = [
            'clientId' => env('PAYCENTER_CLIENT_ID'),
            'reqid' => $reqId,
        ];

        try {
            $response = $client->post($url, [
                'json' => $completeData,
                'headers' => [
                    'Authorization' => 'Bearer ' . env('PAYCENTER_AUTH_TOKEN'),
                    'HMAC' => env('PAYCENTER_HMAC_SECRET'),
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody['responseCode'] === '00') {
                // Payment successful
                return view('payment.success', ['data' => $responseBody]);
            } else {
                // Payment failed
                return view('payment.failed', ['message' => $responseBody['responseText']]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment completion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
