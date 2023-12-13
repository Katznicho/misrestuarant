<?php

namespace App\Http\Controllers;


use App\Payments\Pesapal;
use App\Traits\MessageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use MessageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view("payments.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function testPayments()
    {
    }

    public function finishPayment(Request $request)
    {
        return view("payments.finish");
    }

    public function registerIPN(Request $request)
    {
        try {
            //add validation for url is registered
            $request->validate([
                'url' => 'required|string'
            ]);


            return Pesapal::pesapalRegisterIPN($request->url);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function listIPNS(Request $request)
    {
        try {
            //code...
            $data = Pesapal::listIPNS();
            return response()->json(['success' => true, 'message' => 'Success', 'response' => $data]);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function completePayment(Request $request)
    {
        //         "OrderNotificationType":"IPNCHANGE",
        // 3
        //     "OrderTrackingId":"b945e4af-80a5-4ec1-8706-e03f8332fb04",
        // 4
        //     "OrderMerchantReference":"TEST1515111119"
        //$input =  file_put_contents

        try {
            Log::info('Callback is now working');
            //code...
            Log::info($request->all());
            return response()->json(['success' => true, 'message' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function processOrder(Request $request)
    {
        try {
            //$amount, $phone, $callback
            $request->validate([
                'amount' => 'required|numeric',
                'phone' => 'required|string',
                'callback' => 'required|string',
            ]);
            $amount = $request->input('amount');
            $phone = $request->input('phone');
            $callback = $request->input('callback');
            $data = Pesapal::orderProcess($amount, $phone, $callback);
            return response()->json(['success' => true, 'message' => 'Order processed successfully', 'response' => $data]);
            //code...
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function testSendingMessages(Request $request)
    {
        try {
            //code...
            $message = "Testing sending messages";
            $phoneNumber = "+256759983853";
            $res = $this->sendMessage($phoneNumber, $message);

            return response()->json(['success' => true, 'message' => 'Success', 'response' => $res]);

            return "success";
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
