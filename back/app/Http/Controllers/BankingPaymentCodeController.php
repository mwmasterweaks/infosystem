<?php

namespace App\Http\Controllers;

use App\banking_payment_code;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class BankingPaymentCodeController extends Controller
{
    private $cname = "BankingPaymentCodeController";

    public function index()
    {
        $tbl = banking_payment_code::where("status", "not use")->get();

        return response()->json($tbl);
    }

    public function getall()
    {
        $tbl = banking_payment_code::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $data = banking_payment_code::create($request->all());
            banking_payment_code::insert($data);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new banking_payment_code: " . $data
            );
            return $this->getall();
        } catch (\Exception $ex) {
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
    public function store_multi(Request $request)
    {
        try {
            $data = [];
            foreach ($request->data as $item) {
                $item = (object) $item;
                $excel_date = $item->date; //here is that value 41621 or 41631
                $unix_date = ($excel_date - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;

                $temp = [
                    "code" => $item->code,
                    "amount" => $item->amount,
                    "date" => gmdate("Y-m-d", $unix_date),
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ];
                array_push($data, $temp);
            }
            // return $data;
            banking_payment_code::insert($data);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new banking_payment_code: " . json_encode($data)
            );
            return $this->getall();
        } catch (\Exception $ex) {
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
    }


    public function edit(banking_payment_code $banking_payment_code)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = banking_payment_code::findOrFail($id);
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
                "update banking_payment_code id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->getall();
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
            banking_payment_code::destroy($id);

            return $this->getall();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy1($id, $olt_id)
    {
        try {
            $tbl1 = banking_payment_code::findOrFail($id);
            banking_payment_code::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete banking_payment_code id " . $id .
                    "\nOld banking_payment_code: " . $tbl1
            );
            return $this->getall();
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
