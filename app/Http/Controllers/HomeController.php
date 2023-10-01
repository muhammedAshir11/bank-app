<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    // Amount deposit function start
    public function depositAmount(Request $request){
        try{
            // Create a validator instance for the form input
            $validator = Validator::make($request->all(), [
                'depositAmount' => 'required|numeric|gt:0',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            // Deposit amount
            $transaction = new Transactions;
            $transaction->amount = $request->depositAmount;
            $transaction->user_id = auth::user()->id;
            $transaction->transaction_type = 'credit';
            $transaction->details = 'deposit';
            $result = $transaction->save();

            if($result){
                return response()->json(['message' => 'Deposit successful.'], 200);

            }else{
                return response()->json(['message' => 'Something went wrong!'], 401);
            }


        }catch(\Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }
    // Amount deposit function end

    // Amount withdraw function start
    public function withdrawAmount(Request $request){
        try{

            // Create a validator instance for the form input
            $validator = Validator::make($request->all(), [
                'withdrawAmount' => 'required|numeric|gt:0',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            // Check user balance
            if(Auth::user()->getBalance() < $request->withdrawAmount){
                return response()->json(['message' => 'Insufficient balance'], 422);
            }

            //Withdraw amount
            $transaction = new Transactions;
            $transaction->amount = $request->withdrawAmount;
            $transaction->user_id = auth::user()->id;
            $transaction->transaction_type = 'debit';
            $transaction->details = 'withdraw';
            $result = $transaction->save();


            if($result){
                return response()->json(['message' => 'Withdrawal successful.'], 200);

            }else{
                return response()->json(['message' => 'Something went wrong!'], 401);
            }

        }catch(\Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }
    // Amount withdraw function end 

    // Amount transfer function start
    public function transferAmount(Request $request){
        try{

            $userId = auth()->user()->id;

            // Create a validator instance for the form input
            $validator = Validator::make($request->all(), [
                'transferAmount' => 'required|numeric|gt:0',
                'email' => 'required|email'
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            // Check receiver Exist
            $receiver = User::where('email',$request->email)->first(); 
            if(!$receiver || $receiver->id == $userId){
                return response()->json(['message' => 'Invalid Email'], 422); 
            }

            // Check user balance
            if(Auth::user()->getBalance() < $request->transferAmount){
                return response()->json(['message' => 'Insufficient balance'], 422);
            }


            // Begin a database transaction
            DB::beginTransaction();

            try {
                // Debit the sender's account and credit the receiver's account
                Transactions::create([
                    'amount' => $request->transferAmount,
                    'user_id' => $userId,
                    'transfer_to_id' => $receiver->id,
                    'transaction_type' => 'debit',
                    'details' => 'transfer',
                ]);

                Transactions::create([
                    'amount' => $request->transferAmount,
                    'user_id' => $receiver->id,
                    'transfer_to_id' => $userId,
                    'transaction_type' => 'credit',
                    'details' => 'transfer',
                ]);

                // Commit the transaction if everything is successful
                DB::commit();
                return response()->json(['message' => 'Funds transferred successfully!'], 200);
            } catch (\Exception $e) {
                // Something went wrong, so roll back the transaction
                DB::rollback();
                return response()->json(['message' => "Funds transfer failed: " . $e->getMessage()], 401);
            }

        }catch(\Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }
    
    // Amount transfer function end

    // Fetch transaction and dashboard data start

    public function fetchStatementData(){
        try{
            $userId  = auth()->user()->id;

            // Fetch data where user_id matches the authenticated user's ID
            $statementData = Transactions::with(['secondPartyDetails'])
                ->where('user_id', $userId)
                ->get();

            if($statementData->isNotEmpty()){

                // Render the data using the DataTable component
                $statementTable = view('components.statement-table')
                ->with('statementData', $statementData)
                ->render();

                return response()->json([
                    'statementTable' => $statementTable,
                    'currentBalance' => number_format(auth()->user()->getBalance()),
                ]);
            }else{
                return response()->json([
                    'message' => 'No data found...!',
                ],401);
            }

        }catch(\Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }
    // fetch transaction and dashboard data end
}
