<?php

namespace App\Http\Controllers;

use App\Models\ClassPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    //


    public function HistoryPaymentSearch(Request $request){
        
       try {
            
            $payments = ClassPayment::whereIn('payment_type', $request->payment_type)
                ->whereMonth('pay_month', '=', $request->month) // Filter for the month of August (08)
                ->get();

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payments]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        } 
    }


    public function SingleHistoryPayment(Request $request){
         try {
            $payment = ClassPayment::where('student_id', $request->student_id)->whereIn('status', ['Approved', 'Rejected'])->get();

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payment]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        }
    }
}
