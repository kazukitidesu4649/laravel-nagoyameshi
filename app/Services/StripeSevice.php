<?php

namespace App\Services;

use Stripe\PaymentIntent;

class StripeService
{
    public function createPaymentIntent($paymentMethodId)
    {
        // StripeのPaymentIntentを作成する
        return PaymentIntent::create([
            'payment_method' => $paymentMethodId,
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);
    }
}
