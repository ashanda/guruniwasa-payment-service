<?php

namespace App\Http\Controllers;


use App\Models\ClassPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankPaymentController extends Controller
{
    //


public function studentClassFeesBankPayment(Request $request){

        DB::beginTransaction();
        try {
            // Decode the JSON data from the request
            $cartData = json_decode($request->cartData, true);

            // Check if cartData is an array and has elements
            if (is_array($cartData) && !empty($cartData)) {
                // Initialize a counter for the number of subjects
                $subjectCount = count($cartData); // count the number of subjects in the cart data
                $check_payment = ClassPayment::where('student_id', $request->student_id)->where('pay_month', $request->pay_month)->exists();
                if($check_payment){
                    return response()->json(['status' => 400, 'message' => 'one or more selected subject payment already made']);
                }
                foreach ($cartData as $item) {
                    if (isset($item['data']['student_subjects'])) {
                        $studentSubject = $item['data']['student_subjects'];

                        // Extract subject_id, teacher_id, and grade_id
                        $subjectId = $studentSubject['id']; // or use the appropriate key based on your data structure
                        $teacherId = $studentSubject['tid'];
                        $gradeId = $studentSubject['gid'];
                        $classType = $studentSubject['class_type'];

                        // Calculate fee and apply discount if subject count is 6 or more
                        $fee = $studentSubject['fee']; // Assuming fee is present in the student_subjects data

                        if ($subjectCount >= 6) {
                            // Apply a 25% discount to the fee
                            $discountedFee = $fee - ($fee * 0.25);
                        } else {
                            $discountedFee = $fee; // No discount if subjects are less than 6
                        }

                        // Assuming you want to store this data into a ClassPayment model
                        $payment = new ClassPayment();
                        $payment->student_id = $request->student_id; // assuming this comes from the request
                        $payment->subject_id = $subjectId;
                        $payment->grade_id = $gradeId;
                        $payment->teacher_id = $teacherId;
                        $payment->dateTime = now(); // or use your own datetime format
                        $payment->bank = $request->bank; // assuming this comes from the request
                        $payment->class_type = $classType; // class type
                        $payment->transferSlip = $request->transferSlip; // assuming this comes from the request
                        $payment->pay_month = $request->pay_month; // assuming this comes from the request
                        $payment->payment_type = $request->payment_type; // assuming this comes from the request
                        $payment->payment_id = $request->payment_id; // assuming this comes from the request
                        $payment->fee = $discountedFee; // Store the discounted fee
                        $payment->status = 'Temporarily'; // default value
                        // Add more fields if needed

                        // Save the payment record
                        $payment->save();
                    }
                }

                // If everything is fine, commit the transaction
                DB::commit();

                return response()->json(['status' => 200, 'message' => 'Payments stored successfully']);
            } else {
                // Rollback the transaction in case of invalid data
                DB::rollBack();

                return response()->json(['status' => 400, 'message' => 'Invalid data format or empty data']);
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of any exception
            DB::rollBack();

            // Log the exception message for debugging (optional)
            Log::error('Error storing payments: ' . $e->getMessage());

            // Return an error response
            return response()->json(['status' => 500, 'message' => 'An error occurred while storing payments', 'error' => $e->getMessage()]);
        }
    }


    public function studentPendingPayment(Request $request){
        
        try {
            $payment = ClassPayment::where('status', 'Temporarily')->get();

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payment]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        }

        
    }

    public function studentHistoryPayment(Request $request){
        
        try {
            $payment = ClassPayment::whereIn('status', ['Approved', 'Rejected'])->get();

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payment]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        }

        
    }

    public function studentPayment(Request $request){
        try {
            $payments = ClassPayment::where('payment_id', $request->payment_id)->get();

                // Loop through each payment and update fields if provided in the request
                foreach ($payments as $payment) {
                    // Check if there's an update value for each field and update accordingly
                    
                    if ($request->has('status')) {
                        $payment->status = $request->status;
                    }
                    if ($request->has('staff_member')) {
                        $payment->approved_by = $request->staff_member;
                    }
                    
                    $payment->approved_at = now();
                    // Save the updated payment
                    $payment->save();
                }

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payment]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        }
    }


   public function  studentBankPayment(Request $request){
        try {
            $payment = ClassPayment::whereMonth('pay_month', $request->month)
                        ->where('payment_type', $request->payment_type)
                        ->get();

                // Loop through each payment and update fields if provided in the request
                

            return response()->json(['status' => 200, 'message' => 'Payments retrieved successfully', 'data' => $payment]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An error occurred while retrieving payments', 'error' => $e->getMessage()]);
        }
    }
}
