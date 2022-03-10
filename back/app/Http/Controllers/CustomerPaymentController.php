<?php

namespace App\Http\Controllers;

use App\customer_payment;
use App\Client;
use App\billing;
use App\bill_statement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class CustomerPaymentController extends Controller
{
    private $cname = "CustomerPaymentController";

    public function index()
    {
        $tbl = customer_payment::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = customer_payment::create($request->all());

            $bills = (array) $request->selectedToPay;

            foreach ($bills as $item) {
                $item = (object) $item;
                DB::table("billings")
                    ->where("id", $item->id)
                    ->update(["balance" => $item->payment]);
            }
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new customer_payment: " . $data
            );
            DB::commit();
            return $this->show($request);
        } catch (\Exception $ex) {

            DB::rollBack();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function show($id)
    {
        $tbl = customer_payment::where("client_id", $id)->get();

        return response()->json($tbl);
    }
    public function edit(customer_payment $customer_payment)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = customer_payment::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();
            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update customer_payment id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->show($request->olt_id);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            customer_payment::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy1($id, $olt_id)
    {
        try {
            $tbl1 = customer_payment::findOrFail($id);
            customer_payment::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete customer_payment id " . $id .
                    "\nOld customer_payment: " . $tbl1
            );
            return $this->show($olt_id);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function dailyReport(Request $request)
    {
        $date_from = new Carbon($request->date_from);
        $date_to = new Carbon($request->date_to);
        $area_id = $request->area_id;
        $retVal = [];
        $branches = [];
        array_push($branches, 0);
        foreach ($request->branch_selected as $item) {
            $data = (object)$item;
            array_push($branches, $data->id);
        }
        $tbl = customer_payment::with(["client.branch", "user", "payment_method"])
            ->whereHas("client", function ($query) use ($area_id, $branches) {
                $query->where("area_id", $area_id);
                $query->whereIn("branch_id", $branches);
            })
            ->whereBetween("date", [$date_from->toDateString(), $date_to->toDateString()])
            ->orderBy("payment_method_id")
            ->orderBy("or_number")
            ->get();

        $payID = 0;
        $overAlltotal = 0;
        $totalTemp = 0;
        $totalPerMethod = [];
        $paymethodName = "";
        $x = 0;
        $c = collect();
        $c->put('amount', 'warning');
        $c->put('payment_method_name', 'warning');
        foreach ($tbl as $item) {
            $paymethod = (object) $item->payment_method;
            if ($x == 0) {
                $payID = $item->payment_method_id;
                $x = 1;
            }
            if ($item->payment_method_id != $payID) {

                $payID = $item->payment_method_id;
                $temp = [
                    "payment_method_name" => "Total: " . $paymethodName,
                    "amount" => $totalTemp,
                    "_cellVariants" => $c
                ];

                array_push($retVal, $temp);
                $totalTemp = $item->amount;
                $paymethodName = $paymethod->name;
            } else {
                $paymethodName = $paymethod->name;
                $totalTemp += $item->amount;
            }
            $overAlltotal += $item->amount;
            array_push($retVal, $item);
        }
        $temp = [
            "payment_method_name" => "Total: " . $paymethodName,
            "amount" => $totalTemp,
            "_cellVariants" => $c
        ];

        array_push($retVal, $temp);
        //array_merge($retVal, $totalPerMethod);
        $c1 = collect();
        $c1->put('amount', 'danger');
        $c1->put('payment_method_name', 'danger');
        $temp1 = [
            "remarks" => "Over All Total ",
            "amount" => $overAlltotal,
            "_cellVariants" => $c1
        ];

        array_push($retVal,  $temp1);
        return $retVal;
    }

    public function storePayment(Request $request)
    {
        try {
            DB::beginTransaction();
            //$acc_no = $request->acc_no;
            $ref = $request->base_ref;
            $user_id = $request->user_id;
            $payments = $request->payments;
            $dataToInsert = [];
            foreach ($payments as $payment) {
                $payment = (object) $payment;
                $client_id = 0;
                if ($ref == "statement") {
                    $bill_statements = bill_statement::where('id', $payment->Statement_id)->first();
                    $client_id = $bill_statements->client_id;
                }
                //$client = Client::where('acc_no', $acc_no)->first();
                $amount = $payment->Amount;
                $dup_amount = $amount;
                $remarks = $payment->Remarks;
                $date = $payment->Date;
                $or_number = $payment->OR;
                if ($client_id != 0) {
                    $billings = billing::where("client_id", $client_id)
                        ->where("balance", ">", "0")
                        ->orderBy("date")
                        ->get();
                    $amount_applied = 0;
                    foreach ($billings as $billing) {
                        $temp = $dup_amount - $billing->balance;
                        if ($temp > 0) {
                            DB::table("billings")
                                ->where("id", $billing->id)
                                ->update(["balance" => "0"]);
                            $remarks .= $billing->date . ", ";
                            $amount_applied += $billing->balance;
                            $dup_amount = $temp;
                        } else {
                            $temp = $billing->balance - $dup_amount;
                            DB::table("billings")
                                ->where("id", $billing->id)
                                ->update(["balance" => $temp]);
                            $remarks .= $billing->date;
                            $amount_applied += $dup_amount;
                            $dup_amount = 0;
                            break;
                        }
                    }
                    $payment->amount_applied = $amount_applied;
                    $dataToInsertTemp = [
                        'client_id' => $client_id,
                        'user_id' => $user_id,
                        'payment_method_id' => '309',
                        'amount' => $amount,
                        'date' => $date,
                        'or_number' => $or_number,
                        'remarks' => $remarks
                    ];
                    array_push($dataToInsert, $dataToInsertTemp);
                }
            }
            customer_payment::insert($dataToInsert);
            // $tbl = new customer_payment;
            // $tbl->client_id = $client->id;
            // $tbl->user_id = "0";
            // $tbl->payment_method_id = "309";
            // $tbl->amount = $amount;
            // $tbl->date = Carbon::now();
            // $tbl->or_number = $ref;
            // $tbl->remarks = $remarks;
            // $tbl->tbl_name = "customer_payment";
            // $tbl->created_at = Carbon::now();
            // $tbl->updated_at = Carbon::now();
            // $tbl->save();

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                "Import Payment",
                $this->cname,
                "storePayment",
                "message",
                "Import payment : " . json_encode($payments)
            );
            DB::commit();
            return response()->json($payments);
        } catch (\Exception $ex) {

            DB::rollBack();
            \Logger::instance()->log(
                Carbon::now(),
                "0",
                "711 Kiosk",
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function log($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("mY");
        $myfile = fopen("logs/log" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }
}
