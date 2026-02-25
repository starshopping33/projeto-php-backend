<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller

{
    public function createIntent(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $intent = PaymentIntent::create([
        'amount' => $request->amount,
        'currency' => 'brl',
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    return response()->json([
        'clientSecret' => $intent->client_secret
    ]);
}
}
 

