<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Paystack;
use \App\Plan;
use Illuminate\Support\Facades\Auth;
use \App\User;
use Carbon\Carbon;
use \App\Subscription;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\EmailController;
use \App\Notification;

use App\Transaction;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function __construct()
    {
        // Auth::loginUsingId(20);
        $this->middleware('auth');
    }

    public function redirectToGateway(Request $request)
    {
        // return Paystack::getAuthorizationUrl()->redirectNow();
        try {
            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch (\Exception $e) {
            return Redirect::back()->withMessage(['msg' => 'The paystack token has expired. Please refresh the page and try again.', 'type' => 'error']);
        }
    }

    public function redirectToPay(Request $request)
    {
        $plan = Plan::where('id', $request->input('plan_id'))->first();

        $total_price = $plan->price * 100;

        $r = [
            'email' => Auth()->User()->email,
            'amount' => $total_price,
            'quantity' => 1,
            'currency' => 'NGN',
            'reference' => Paystack::genTranxRef(),
            'orderID' => Auth()->User()->id,
            'metadata' => json_encode($plan->id)
        ];

        // $r = [
        //     'email' => 'lawrenceajayi481@gmail.com',
        //     'amount' => $total_price,
        //     'quantity' => 1,
        //     'currency' => 'NGN',
        //     'reference' => Paystack::genTranxRef(),
        //     'orderID' => 345,
        //     'metadata' => json_encode($plan->id)
        // ];

        return redirect()->action(
            'PaymentController@redirectToGateway',
            $r
        );
    }

    public function paywithoutsignup(Request $request)
    {
        $plan = Plan::where('id', $request->input('plan_id'))->first();

        $total_price = $plan->price * 100;

        $r = [
            'email' => $request->email,
            'amount' => $total_price,
            'quantity' => 1,
            'currency' => 'NGN',
            'reference' => Paystack::genTranxRef(),
            'orderID' => $request->email . time(),
            'metadata' => json_encode($plan->id)
        ];

        // $r = [
        //     'email' => 'lawrenceajayi481@gmail.com',
        //     'amount' => $total_price,
        //     'quantity' => 1,
        //     'currency' => 'NGN',
        //     'reference' => Paystack::genTranxRef(),
        //     'orderID' => 345,
        //     'metadata' => json_encode($plan->id)
        // ];

        return redirect()->action(
            'PaymentController@redirectToGateway',
            $r
        );
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        $plan_id = $paymentDetails['data']['metadata'];
        // $plan_id = 3;

        $plan = Plan::where('id', $plan_id)->first();

        if ($paymentDetails['status']) {
            // return response()->json($paymentDetails);
            $email = $paymentDetails['data']['email'];
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'plan_id' => $plan->id,
                    'started_at' => Carbon::now(),
                    'ended_at' => Carbon::now()->addDays($plan->days)
                ]);

                $sub = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'clients' => $plan->clients,
                    'posts' => $plan->posts,
                    'accounts' => $plan->accounts,
                    'remove_social' => $plan->remove_social,
                    'started_at' => $user->started_at,
                    'ended_at' => $user->ended_at
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'message' => "You've successfully subscribe to " . $plan->name . ". Subscription will expire on " . date('d, M. Y', strtotime($user->ended_at))
                ]);
                 
                Transaction::create([
                    'user_id' => $user->id,
                    'subscription_id' => $sub->id,
                    'method' => 'paystack',
                    'ref' => $paymentDetails['data']['reference'],
                    'status' => 1
                ]);

                session(['postslate_id' => $user->id]);

                (new EmailController())->sendSubscriptionEmail([
                    'name' => " ",
                    'plan_name' => $plan->name,
                    'email' => $user->email
                ]);

                return redirect('/sign-up');
            } else {
                $user->plan_id = $plan->id;
                $user->started_at = Carbon::now();
                $user->ended_at = Carbon::now()->addDays($plan->days);
                $user->save();

                $prevSub = (new UserController())->checkSubcription();
                if ($prevSub) {
                    if ($prevSub->plan_id != 1) {
                        $plan->clients += $prevSub->clients;
                        $plan->posts += $prevSub->posts;
                        $plan->accounts += $prevSub->accounts;
                        $plan->remove_social += $prevSub->remove_social;
                    }
                }

                $sub = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'clients' => $plan->clients,
                    'posts' => $plan->posts,
                    'accounts' => $plan->accounts,
                    'remove_social' => $plan->remove_social,
                    'started_at' => $user->started_at,
                    'ended_at' => $user->ended_at
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'message' => "You've successfully subscribe to " . $plan->name . ". Subscription will expire on " . date('d, M. Y', strtotime($user->ended_at))
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'subscription_id' => $sub->id,
                    'method' => 'paystack',
                    'ref' => $paymentDetails['data']['reference'],
                    'status' => 1
                ]);
                (new EmailController())->sendSubscriptionEmail([
                    'name' => $user->last_name . " " . $user->first_name,
                    'plan_name' => $plan->name,
                    'email' => $user->email
                ]);
                
                $data = ['msg' => 'User account successfully upgraded'];

                return redirect('/payment?success=true');
            }

            // $user = User::find(Auth::user()->id);
            // $user = User::find(25);
           
        }
        $data = ['error' => 'Payment failed, pls try again'];
        return redirect('/payment?success=false');
    }
}
