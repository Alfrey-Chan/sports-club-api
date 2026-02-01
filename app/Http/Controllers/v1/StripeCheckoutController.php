<?php

namespace App\Http\Controllers\v1;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeCheckoutController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'planKey' => ['required', 'string', 'in:2_per_week,3_per_week,4_per_week,5_per_week'],
            'language' => ['required', 'string', 'in:en,ja'],
        ]);

        $priceId = config('stripe.prices.' . $validated['planKey']);

        if (!$priceId) {
            return response()->json([
                'message' => 'Stripe price is not configured for this plan.',
            ], 500);
        }

        Stripe::setApiKey(config('stripe.secret'));
        
        $frontendUrl = config('stripe.frontend_url');

        $session = Session::create([                                                                                                       
            'mode' => 'subscription',      
            'locale' => $validated['language'],             
            'customer' => config('stripe.test_customer'),   
            'saved_payment_method_options' => [                                                                                            
                'allow_redisplay_filters' => ['always', 'limited', 'unspecified'],                                                                                 
            ],                                                                                                                                                                                                          
            'line_items' => [[                                                                                                             
                'price' => $priceId,                                                                                                       
                'quantity' => 1,                                                                                                           
            ]],                                                                                                                            
            'success_url' => $frontendUrl . 'billing/success?session_id={CHECKOUT_SESSION_ID}',                                           
            'cancel_url'  => $frontendUrl . 'select-plan?canceled=1',                                                                           
            'metadata' => [                                                                                                                
                'planKey' => $validated['planKey'],                                                                                        
            ],                                                                                                                             
        ]);  

        return response()->json([
            'url' => $session->url,
        ]);
    }
}
