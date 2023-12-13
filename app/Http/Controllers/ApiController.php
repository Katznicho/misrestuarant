<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\MessageTrait;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    use UserTrait, MessageTrait;

    public function logoutCustomer(Request $request)
    {
        // $request->user()->currentAccessToken()->delete();
        return response()->json([
            'response' => 'success',
            'message' => 'Successfully logged out!'
        ], 200);
    }

    public function loginCustomerWithPhoneNumber(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|string'
        ]);

        $phoneNumber = $this->formatMobileInternational($request->phoneNumber);

        // Find the customer
        $customer = Customer::where('phone', $phoneNumber)->first();

        // Check if the customer exists
        if (!$customer) {
            return response()->json([
                'response' => 'failure',
                'message' => 'Invalid credentials'
            ], 401);
        } else {
            return response()->json([
                'response' => 'success',
                'customer' => $customer
            ], 200);
        }
    }

    public function validateCustomerPin(Request $request)
    {
        try {
            $request->validate([
                'phoneNumber' => 'required|string',
                'pin' => 'required|string'
            ]);
            $phoneNumber = $this->formatMobileInternational($request->phoneNumber);

            // Find the customer
            $customer = Customer::where('phone', $phoneNumber)->first();

            if (!$customer) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $hashed_pin = Hash::make($request->pin);
            if ($hashed_pin != $customer->pin) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }


            return response()->json([
                'response' => 'success',
                'customer' => $customer
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //get customer balance
    public function getCustomerBalance(Request $request)
    {
        try {
            $request->validate([
                'phoneNumber' => 'required|string'
            ]);

            $phoneNumber = $this->formatMobileInternational($request->phoneNumber);

            // Find the customer
            $customer = Customer::where('phone', $phoneNumber)->first();

            if (!$customer) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }

            return response()->json([
                'response' => 'success',
                'balance' => $customer->account_balance
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //get customer transactions
    public function getCustomerTransactions(Request $request)
    {
        try {
            $request->validate([
                'phoneNumber' => 'required|string'
            ]);

            $phoneNumber = $this->formatMobileInternational($request->phoneNumber);
            $customer = Customer::where('phone', $phoneNumber)->first();

            if (!$customer) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $transactions = Transaction::where('customer_id', $customer->id)->get();

            return response()->json([
                'response' => 'success',
                'transactions' => $transactions
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //change pin
    public function changeCustomerPin(Request $request)
    {
        try {
            $request->validate([
                'phoneNumber' => 'required|string',
                'odPin' => 'required|string|min:4|max:4',
                'newPin' => 'required|string|min:4|max:4|confirmed',
            ]);

            $phoneNumber = $this->formatMobileInternational($request->phoneNumber);

            // Find the customer
            $customer = Customer::where('phoneNumber', $phoneNumber)->first();

            if (!$customer) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $hashed_odPin = Hash::make($request->odPin);
            if ($hashed_odPin != $customer->pin) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $hashed_newPin = Hash::make($request->newPin);
            $customer->pin = $hashed_newPin;
            $customer->save();
            //send message to customer
            $message = "Your new pin is " . $request->newPin . "If you did not make this request, please contact us.";
            $this->sendMessage($customer->phoneNumber, $message);
            return response()->json([
                'response' => 'success',
                'customer' => $customer
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            // Find the user
            $user = User::where('email', $request->email)->with("branch")->first();

            // Check if the user exists and the password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Create an auth token for the user
            $authToken = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'response' => 'success',
                'message' => 'Successfully logged in!',
                'user' => $user,
                'authToken' => $authToken
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            //code...
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'response' => 'success',
                'message' => 'Successfully logged out!'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'response' => 'failure',
                'message' => $th
            ], 500);
        }
    }

    public function getCustomer(Request $request)
    {
        try {
            $request->validate([
                'card_number' => 'required|string'
            ]);
            $cardDetails = Card::where('card_number', $request->card_number)->first();
            if ($cardDetails) {
                $customer = Customer::where('card_id', $cardDetails->id)->with("card")->first();
                return response()->json([
                    'response' => 'success',
                    'customer' => $customer,
                    'card' => $cardDetails
                ], 200);
            } else {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Card not found'
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deposit(Request $request)
    {
        try {
            //code...
            $request->validate([
                'card_number' => 'required|string',
                'amount' => 'required|numeric',
                'type' => 'required|string',
            ]);
            $cardDetails = Card::where('card_number', $request->card_number)->first();

            if ($cardDetails) {
                $customer = Customer::where('card_id', $cardDetails->id)->first();
                //create  a transaction
                Transaction::create([
                    'amount' => $request->amount,
                    'type' => $request->type ?? 'Credit',
                    'user_id' => $this->getCurrentLoggedUserBySanctum()->id,
                    'branch_id' => $this->getCurrentLoggedUserBySanctum()->branch_id,
                    'customer_id' => $customer->id,
                    'payment_mode' => "Cash",
                    'reference' => Str::random(10),
                    'status' => "completed",
                    'phone_number' => $customer->phone,
                    "description" => $request->description ?? "Deposit",

                ]);

                $customer->account_balance += $request->amount;
                $customer->save();

                return response()->json([
                    'response' => 'success',
                    'message' => 'Amount deposited successfully'
                ], 200);
            } else {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Card not found'
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
        //
    }

    public function withdraw(Request $request)
    {
        try {
            //code...
            $request->validate([
                'card_number' => 'required|string',
                'amount' => 'required|numeric',
                'type' => 'required|string',
            ]);
            $cardDetails = Card::where('card_number', $request->card_number)->first();

            if ($cardDetails) {
                $customer = Customer::where('card_id', $cardDetails->id)->first();
                if ($customer->account_balance <= $request->amount) {
                    return response()->json([
                        'response' => 'failure',
                        'message' => 'Insufficient balance'
                    ], 404);
                }
                //create  a transaction
                Transaction::create([
                    // 'card_id' => $cardDetails->id,
                    'amount' => $request->amount,
                    'type' => $request->type ?? 'Debit',
                    'user_id' => $this->getCurrentLoggedUserBySanctum()->id,
                    'branch_id' => $this->getCurrentLoggedUserBySanctum()->branch_id,
                    'customer_id' => $customer->id,
                    'payment_mode' => "Cash",
                    'reference' => Str::random(10),
                    'status' => "completed",
                    'phone_number' => $customer->phone,
                    "description" => $request->description ?? "Withdrawal",
                ]);

                $customer->account_balance -= $request->amount;
                $customer->save();

                return response()->json([
                    'response' => 'success',
                    'message' => 'Amount withdrawn successfully'
                ], 200);
            } else {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Card not found'
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage(),
            ], 500);
        }
        //
    }

    //get all transaction with current user_id
    public function getTransactionsByUserId()
    {
        try {
            //code...
            $transactions = Transaction::where('user_id', $this->getCurrentLoggedUserBySanctum()->id)
                ->with('customer')
                ->with('branch')
                ->with('user')
                ->get();
            return response()->json([
                'response' => 'success',
                'transactions' => $transactions
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
        //

    }

    public  function  getAllUsers(Request $request)
    {
        try {
            $users = User::all();
            return $users;
        } catch (\Throwable $throwable) {
            return response()->json([
                'response' => 'failure',
                'message' => $throwable->getMessage()
            ], 500);
        }
    }
}
