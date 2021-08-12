<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Subscription;
use App\SubscriptionPayment;
use Carbon\Carbon;

class SubscriptionsController extends Controller
{
    //

    public function needToSubscribe()
    {

        $subscription_payment = SubscriptionPayment::where('status', 'pending')->first();
        if ($subscription_payment->payment_type == 'initial') {
            return 'subscription needed';
        } else {

            $today = date('Y-m-d', strtotime('now'));
            $recurrent_subscription = $subscription_payment;
            if ($recurrent_subscription->due_date <= $today) {
                return 'subscription needed';
            }
        }
        return 'subscription not needed';
    }

    public function fetchSubscription()
    {
        $subscription_payments = SubscriptionPayment::orderBy('id', 'DESC')->get();
        $subscription_payment = SubscriptionPayment::where('status', 'pending')->first();
        if ($subscription_payment->payment_type == 'initial') {
            return response()->json(compact('subscription_payment', 'subscription_payments'), 200);
        } else {

            $today = date('Y-m-d', strtotime('now'));
            if ($subscription_payment->due_date <= $today) {
                return response()->json(compact('subscription_payment', 'subscription_payments'), 200);
            }
        }
        return response()->json(compact('subscription_payments'), 200);
    }

    public function subscriptionPayment(Request $request)
    {
        $status = $request->status;
        $payment_id = $request->payment_id;
        if ($status === 'success') {
            $subscription_payment = SubscriptionPayment::find($payment_id);
            $subscription_payment->paid = $request->amount;
            $subscription_payment->transaction = $request->transaction;
            $subscription_payment->status = $status;
            $subscription_payment->message = $request->message;

            if ($subscription_payment->save()) {
                # code...
                $this->createNextDueSubscription();
            }
        }
        return 'success';
    }

    private function createNextDueSubscription()
    {
        $due_date = Carbon::now()->endOfMonth();
        $subscription = Subscription::where('status', 'active')->first();
        $subscription_payment = new SubscriptionPayment();
        if ($subscription->type == 'yearly') {
            $due_date = Carbon::now()->endOfYear();
        }
        $subscription_payment->amount_due = $subscription->amount_due;
        $subscription_payment->due_date = $due_date;
        $subscription_payment->payment_type = $subscription->type;
        $subscription_payment->save();
    }
}
